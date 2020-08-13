<?php

/**
 * Custom Payment Gateway for Restrict Content Pro and Digistore24
 */

class RCP_Payment_Gateway_Digistore extends RCP_Payment_Gateway {

	/**
	 * @var string
	 */
	protected $api_key;

	/**
	 * @var string
	 */
	protected $ipn_passphrase;

	/**
	 * Declare feature support and set up any environment variables like API key(s), endpoint URL, etc.
	 */
	public function init() {
		global $rcp_options;

		$this->supports[] = 'one-time';
		$this->supports[] = 'recurring';

		$this->api_key        = $rcp_options['digistore_api_key'];
		$this->ipn_passphrase = $rcp_options['digistore_ipn_passphrase'];

		require_once __DIR__ . '/ds24_api.php';
	}

	/**
	 * Process registration with DigiStore API
	 *
	 * @link https://docs.digistore24.com/article-categories/api/
	 */
	public function process_signup() {
		global $rcp_options;
		global $rcp_levels_db;

		try {
			$product_id = $rcp_levels_db->get_meta( $this->subscription_id, 'digistore_product_id', true );

			$api = DigistoreApi::connect( $this->api_key );

			$user = get_user_by( 'id', $this->user_id );

			$buyer = array(
				'email'      => $this->email ?? '',
				'first_name' => $user->first_name ?? '',
				'last_name'  => $user->last_name ?? '',
			);

			$payment_plan = array(
				'currency'     => $this->currency,
				'first_amount' => $this->initial_amount,
			);

			if ( $this->auto_renew ) {
				$billing_interval = $this->length . '_' . $this->length_unit;

				$payment_plan['other_amounts']           = $this->amount;
				$payment_plan['first_billing_interval']  = $billing_interval;
				$payment_plan['other_billing_intervals'] = $billing_interval;
			}

			// Add pre-launch testphase.
			// DigiStore testphase starts the next day and ends the day before payment.
			// So users on the day before launch have to pay immediately.
			global $crv_launch_date;
			if ( isset( $crv_launch_date ) ) {
				$now        = new DateTime();
				$launch_day = ( clone $crv_launch_date )->setTime( 0, 0, 0 );
				$full_days  = date_diff( $now, $launch_day )->days;

				if ( 0 < $full_days ) {
					$payment_plan['test_interval'] = $full_days . '_day';
				}
			}

			$tracking = array(
				'custom' => $this->user_id . '|' . absint( $this->membership->get_id() ),
			);

			// For upgrades add the old affiliate (only when valid).
			if ( $this->membership->was_upgrade() ) {
				$old_membership_id = $this->membership->get_upgraded_from();
				$affiliate_id      = rcp_get_membership_meta( $old_membership_id, 'digistore_affiliate', true );

				if ( $affiliate_id ) {
					$api_response = $api->validateAffiliate( $affiliate_id, $product_id );

					if ( 'invalid_affiliate_name' !== $api_response->affiliation_status ) {
						$tracking['affiliate'] = $affiliate_id;
					}
				}
			}

			$valid_until = '2h';

			$urls = array(
				'thankyou_url' => $this->return_url,
				'fallback_url' => get_permalink( $rcp_options['registration_page'] ),
			);

			$placeholders = array();

			$settings = array();

			$api_response = $api->createBuyUrl( $product_id, $buyer, $payment_plan, $tracking, $valid_until, $urls, $placeholders, $settings );

			wp_redirect( $api_response->url );

		} catch ( DigistoreApiException $error ) {

			$this->error_message = $error->getMessage();
			do_action( 'rcp_registration_failed', $this );

			$errormsg  = '<p>' . __( 'An unidentified error occurred.', 'rcp' ) . '</p>';
			$errormsg .= '<p>' . $error->getMessage() . '</p>';

			wp_die( wp_kses_post( $errormsg ), esc_html__( 'Error', 'rcp' ), array( 'response' => '401' ) );

		} finally {
			$api->disconnect();
		}

		exit;
	}

	/**
	 * Process Digistore IPN - Log payments and edit membership.
	 *
	 * @link https://docs.digistore24.com/knowledge-base/ipn-guide/
	 */
	public function process_webhooks() {
		if ( ! isset( $_GET['listener'] ) || strtolower( $_GET['listener'] ) !== 'digistore' ) {
			return;
		}

		rcp_log( 'Starting to process DigiStore24 IPN' );

		// When not in test mode, check the signature
		if ( ! $this->test_mode && ! static::is_valid_digistore_signature( $this->ipn_passphrase, $_POST ) ) {
			rcp_log( 'Digistore IPN: Invalid SHA Signature', true );
			die( 'ERROR: invalid sha signature' );
		}

		$user_id    = 0;
		$posted     = apply_filters( 'rcp_ipn_post', $_POST );
		$membership = false;
		// sent by `process_signup()`: array [user_id, membership_id]
		$custom = ! empty( $posted['custom'] ) ? explode( '|', $posted['custom'] ) : false;

		/**
		 * Get membership object by order id
		 *
		 * An order_id can have multiple items, which all get their own ipn call (but share the order_id).
		 * Each order item has its own transaction.
		 * And payment_id changes for each transaction, so order_id is the only unique ipn id for a membership.
		 */
		if ( ! empty( $posted['order_id'] ) ) {
			$membership = rcp_get_membership_by( 'gateway_subscription_id', $posted['order_id'] );
		}

		// get membership object by membership_id in custom field (sent by `process_signup()`)
		if ( empty( $membership ) && ! empty( $custom[1] ) ) {
			$membership = rcp_get_membership( absint( $custom[1] ) );
		}

		// membership object not found
		if ( empty( $membership ) || ! $membership->get_id() > 0 ) {
			rcp_log( 'Exiting DigiStore IPN - membership ID not found.', true );
			die( 'no membership found' );
		}

		$this->membership = $membership;

		rcp_log( sprintf( 'Processing IPN for membership #%d.', $membership->get_id() ) );

		if ( empty( $user_id ) ) {
			$user_id = $membership->get_customer()->get_user_id();
		}

		$member = new RCP_Member( $membership->get_customer()->get_user_id() ); // for backwards compatibility

		$membership_level_id = $membership->get_object_id();
		if ( ! $membership_level_id ) {
			rcp_log( 'Exiting DigiStore IPN - no membership level ID.', true );
			die( 'no membership level found' );
		}

		$membership_level = rcp_get_subscription_details( $membership_level_id );
		if ( ! $membership_level ) {
			rcp_log( 'Exiting DigiStore IPN - no membership level found.', true );
			die( 'no membership level found' );
		}

		$amount = isset( $posted['transaction_amount'] )
		? number_format( (float) $posted['transaction_amount'], 2, '.', '' )
		: false;

		// Set the DigiStore Affiliate.
		if ( $posted['affiliate_name'] ) {
			rcp_update_membership_meta( $membership->get_id(), 'digistore_affiliate', $posted['affiliate_name'] );
		}

		// setup the payment info in an array for storage
		$payment_data = array(
			'subscription'     => $membership_level->name,
			'payment_type'     => $posted['pay_method'] ?? '(not provided)',
			'subscription_key' => $membership->get_subscription_key(),
			'user_id'          => $user_id,
			'customer_id'      => $membership->get_customer()->get_id(),
			'membership_id'    => $membership->get_id(),
			'status'           => 'complete',
			'gateway'          => $membership->get_gateway(),
		);

		if ( false !== $amount ) {
			$payment_data['amount'] = $amount;
		}

		if ( ! empty( $posted['transaction_date'] ) ) {
			$payment_data['date'] = date( 'Y-m-d H:i:s', strtotime( $posted['transaction_date'] ) );
		}

		if ( ! empty( $posted['transaction_id'] ) ) {
			$payment_data['transaction_id'] = sanitize_text_field( $posted['transaction_id'] );
		}

		$rcp_payments       = new RCP_Payments();
		$pending_payment_id = rcp_get_membership_meta( $membership->get_id(), 'pending_payment_id', true );

		switch ( $posted['event'] ) :

			case 'on_payment': // (initial or renewal) payment successful or trial (amount 0)
				rcp_log( 'Processing DigiStore on_payment IPN.' );

				$pay_sequence_no = $posted['pay_sequence_no'];

				if ( isset( $posted['billing_type'] ) && 'installment' == $posted['billing_type'] ) {
					rcp_log( 'Installments not supported.' );
					break;
				}

				$is_single_payment  = 0 == $pay_sequence_no;
				$is_initial_payment = 1 == $pay_sequence_no;

				if ( $is_single_payment || $is_initial_payment ) {
					$this->membership->set_gateway_subscription_id( $posted['order_id'] );

					if ( ! empty( $pending_payment_id ) ) {
						// This activates the membership automatically
						$rcp_payments->update( $pending_payment_id, $payment_data );
						$payment_id = $pending_payment_id;
					} else {
						$payment_id = $rcp_payments->insert( $payment_data );
						if ( $is_single_payment ) {
							$this->membership->renew( false );
						}
					}
				} else { // Renewal payment.
					$payment_data['transaction_type'] = 'renewal';
					$payment_id                       = $rcp_payments->insert( $payment_data );
				}

				// Renew recurring membership according to DigiStore (or fall back to today).
				$is_recurring = $pay_sequence_no > 0;
				if ( $is_recurring ) {
					$this->renew_recurring_membership( $posted );

					do_action( 'rcp_webhook_recurring_payment_processed', $member, $payment_id, $this );
				}

				if ( isset( $posted['invoice_url'] ) ) {
					$rcp_payments->add_meta( $payment_id, 'digistore_invoice_url', $posted['invoice_url'] );
				}

				do_action( 'rcp_gateway_payment_processed', $member, $payment_id, $this );

				break;

			case 'on_payment_missed':
				rcp_log( 'Processing DigiStore on_payment_missed IPN.' );

				$this->webhook_event_id = $posted['transaction_id'];

				do_action( 'rcp_recurring_payment_failed', $member, $this );

				$membership->expire();

				break;

			case 'on_refund': // payment got refunded by digistore
			case 'on_chargeback':
				rcp_log( 'Processing DigiStore on_refund or on_chargeback IPN.' );

				$order_id = $posted['order_id'];

				if ( empty( $posted['parent_transaction_id'] ) ) {
					rcp_log( 'Could not find payment to cancel.' );
					break;
				}

				// get original payment by transaction id
				$cancelled_payment = $rcp_payments->get_payment_by( 'transaction_id', $posted['parent_transaction_id'] );

				$rcp_payments->update( $cancelled_payment->id, array( 'status' => 'refunded' ) );

				$membership->expire();

				break;

			case 'on_rebill_cancelled':
				rcp_log( 'Processing DigiStore on_rebill_cancelled IPN.' );

				// If this is a completed payment plan, we can skip any cancellation actions.
				if ( $this->membership->has_payment_plan() && $this->membership->at_maximum_renewals() ) {
					rcp_log( sprintf( 'Membership #%d has completed its payment plan - not cancelling.', $this->membership->get_id() ) );
					break;
				}

				// only mark the membership as cancelled, react at missed payment only
				if ( $this->membership->is_active() ) {
					$this->membership->cancel();
				}

				do_action( 'rcp_webhook_cancel', $member, $this );

				break;

			case 'on_rebill_resumed':
				rcp_log( 'Processing DigiStore on_rebill_resumed IPN.' );

				$this->membership->set_status( 'active' );

				break;

			default: // unhandeled or unknown event
				break;

		endswitch;

		die( 'OK' );
	}


	/**
	 * Renews a recurring membership and sets expiration date
	 * to the end of the day the posted 'next_payment_at' date, if it is set,
	 * otherwise it renews the membership according to RCP rules.
	 */
	private function renew_recurring_membership( $posted ) {
		if ( isset( $posted['next_payment_at'] ) ) {
			$this->membership->renew( true, 'active', static::end_of_day( $posted['next_payment_at'] ) );
		} else {
			$this->membership->renew( true );
		}
	}

	/**
	 * Calculates the end of the day.
	 *
	 * @param string next_payment_at parameter, i.e. '2020-04-09'
	 * @return string expiration date in MySQL datetime format, i.e. '2020-04-09 23:59:59'
	 */
	private static function end_of_day( $next_payment_at ) {
		return date( 'Y-m-d', strtotime( $next_payment_at ) ) . ' 23:59:59';
	}


	/**
	 * Stops rebilling by calling the DigiStore API
	 *
	 * @param   string $gateway_subscription_id - DigiStore order_id
	 * @return  true|WP_Error true on success, WP_Error on cancellation error
	 */
	public function stop_rebilling( $gateway_subscription_id ) {
		try {
			$api = DigistoreApi::connect( $this->api_key );

			$api->stopRebilling( $gateway_subscription_id );

			$api->disconnect();
		} catch ( DigistoreApiException $error ) {
			return new WP_Error( 'digistore_api_error', $error->getMessage() );
		}
		return true;
	}

	/**
	 * Check if signature sent by digistore is valid.
	 *
	 * @param string $ipn_passphrase The passphrase set in digistore backoffice
	 * @param array  $ipn_data Information recieved from digistore ipn
	 * @return bool if the signature is valid
	 */
	private static function is_valid_digistore_signature( $ipn_passphrase, $ipn_data ) {
		$received_sha_sign = $ipn_data['sha_sign'];

		$sha_sign = static::create_digistore_ipn_signature( $ipn_passphrase, $ipn_data );

		return $sha_sign === $received_sha_sign;
	}

	/**
	 * Create the SHA signature used by DigiStore IPN
	 *
	 * @param string $ipn_passphrase The passphrase set in digistore backoffice
	 * @param array  $ipn_data Data recieved from digistore ipn
	 * @return string the SHA signature
	 */
	private static function create_digistore_ipn_signature( $ipn_passphrase, $ipn_data ) {
		$keys = array_keys( $ipn_data );
		sort( $keys );

		$sha_string = '';

		foreach ( $keys as $key ) {
			$value = html_entity_decode( $ipn_data[ $key ] );

			$is_empty = ! isset( $value ) || $value === '' || $value === false;

			if ( $is_empty || $key === 'sha_sign' ) {
				continue;
			}

			$sha_string .= "$key=$value$ipn_passphrase";
		}

		$sha_sign = strtoupper( hash( 'sha512', $sha_string ) );

		return $sha_sign;
	}
}
