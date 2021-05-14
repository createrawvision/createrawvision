<?php

/**
 * Adds DigiStore as a payment gateway for Restrict Content Pro
 *
 * Plugin Name:       RCP Payment Gatway DigiStore
 * Description:       Adds DigiStore as a payment gateway for Restrict Content Pro
 * Version:           1.0.0
 * Author:            Josef Wittmann
 */

/**
 * Register custom payment gateways for restrict content pro
 */
function crv_rcp_register_custom_gateways( $gateways ) {
	require_once __DIR__ . '/class-rcp-payment-gateway-digistore.php';

	$gateways['digistore'] = array(
		'label'       => 'Digistore',
		'admin_label' => 'Digistore',
		'class'       => 'RCP_Payment_Gateway_Digistore',
	);

	return $gateways;
}
add_filter( 'rcp_payment_gateways', 'crv_rcp_register_custom_gateways' );


/**
 * Allow DigiStore subscriptions to be cancelled
 */
function crv_allow_digistore_cancellation( $can_cancel, $membership_id, $membership ) {
	if (
	$membership->is_recurring()
	&& 'active' == $membership->get_status()
	&& $membership->is_paid()
	&& ! $membership->is_expired()
	&& 'digistore' == $membership->get_gateway()
	) {
		return true;
	}

	return $can_cancel;
}
add_filter( 'rcp_membership_can_cancel', 'crv_allow_digistore_cancellation', 10, 3 );

function crv_cancel_digistore_subscription( $success, $gateway, $gateway_subscription_id ) {
	if ( $gateway != 'digistore' ) {
		return $success;
	}

	$gateways          = new RCP_Payment_Gateways();
	$gateway_digistore = $gateways->get_gateway( 'digistore' );
	$gateway_digistore = new $gateway_digistore['class']();

	return $gateway_digistore->stop_rebilling( $gateway_subscription_id );
}
add_filter( 'rcp_membership_payment_profile_cancelled', 'crv_cancel_digistore_subscription', 10, 3 );


/**
 * Add a link to the DigiStore receipt_url to the subscription_details shortcode
 */
add_action(
	'rcp_subscription_details_action_links',
	function( $links, $membership ) {
		$digistore_receipt_url = rcp_get_membership_meta( $membership->get_id(), 'digistore_receipt_url', true );

		if ( $digistore_receipt_url ) {
			echo '<br><a href="' . esc_url( $digistore_receipt_url ) . '">Zu DigiStore24</a>';
		}
	},
	10,
	2
);


/**
 * Redirect invoices to digistore by checking payment meta to include invoice url
 */
function crv_trigger_digistore_invoice_download() {
	if ( ! isset( $_GET['rcp-action'] ) || 'download_invoice' != $_GET['rcp-action'] ) {
		return;
	}

	global $rcp_payments_db;

	$payment_id = absint( $_GET['payment_id'] );

	$digistore_invoice_url = $rcp_payments_db->get_meta( $payment_id, 'digistore_invoice_url', true );

	if ( empty( $digistore_invoice_url ) ) {
		return;
	}

	wp_redirect( $digistore_invoice_url );
	exit;
}
add_action( 'init', 'crv_trigger_digistore_invoice_download', 9 );


/**
 * Add DigiStore product specific settings to the subscription level form
 */
function crv_digistore_level_form_fields( $product_id = '', $affiliate_commission_fix = '' ) {
	?>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="crv-digistore-product-id"><?php _e( 'DigiStore Product-ID' ); ?></label>
		</th>
		<td>
			<input style="width: 100px;" id="crv-digistore-product-id" type="text" name="digistore_product_id" value="<?php echo esc_attr( $product_id ); ?>" />
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="crv-digistore-affiliate-commission-fix"><?php _e( 'DigiStore Fixed Affiliate Commision' ); ?></label>
		</th>
		<td>
			<input style="width: 100px;" id="crv-digistore-affiliate-commission-fix" type="text" name="digistore_affiliate_commission_fix" value="<?php echo esc_attr( $affiliate_commission_fix ); ?>"  pattern="^(\d+\.\d{1,2})|(\d+)$"/>
			<p class="description">Overwrites the fixed affiliate commission. Leave empty to use settings from DigiStore backoffice.</p>
		</td>
	</tr>
	<?php
}

function crv_digistore_level_form_fields_edit( $level ) {
	global $rcp_levels_db;
	$product_id               = $rcp_levels_db->get_meta( $level->id, 'digistore_product_id', true );
	$affiliate_commission_fix = $rcp_levels_db->get_meta( $level->id, 'digistore_affiliate_commission_fix', true );

	crv_digistore_level_form_fields( $product_id, $affiliate_commission_fix );
}

add_action( 'rcp_add_subscription_form', 'crv_digistore_level_form_fields' );
add_action( 'rcp_edit_subscription_form', 'crv_digistore_level_form_fields_edit' );

/**
 * Add the DigiStore Product ID as subscription level meta
 */
function crv_update_digistore_level_meta( $level_id, $args ) {
	global $rcp_levels_db;
	if ( $args['digistore_product_id'] ) {
		$rcp_levels_db->update_meta( $level_id, 'digistore_product_id', trim( $args['digistore_product_id'] ) );
	}
	if ( $args['digistore_affiliate_commission_fix'] ) {
		$rcp_levels_db->update_meta( $level_id, 'digistore_affiliate_commission_fix', trim( $args['digistore_affiliate_commission_fix'] ) );
	}
}
add_action( 'rcp_add_subscription', 'crv_update_digistore_level_meta', 10, 2 );
add_action( 'rcp_edit_subscription_level', 'crv_update_digistore_level_meta', 10, 2 );


/**
 * Add DigiStore Settings
 */
function crv_rcp_digistore_settings( $rcp_options ) {
	?>
  <table class="form-table">
	<tr valign="top">
	  <th colspan=2>
		<h3><?php _e( 'DigiStore Settings', 'rcp' ); ?></h3>
	  </th>
	</tr>
	<tr>
	  <th>
		<label for="rcp_settings[digistore_api_key]"><?php _e( 'DigiStore API Key', 'rcp' ); ?></label>
	  </th>
	  <td>
		<input type="text" class="regular-text" style="width: 300px;" name="rcp_settings[digistore_api_key]" id="rcp_settings[digistore_api_key]" value="<?php echo isset( $rcp_options['digistore_api_key'] ) ? esc_attr( $rcp_options['digistore_api_key'] ) : ''; ?>" />
		<p class="description"><?php _e( 'Enter your DigiStore API key.', 'rcp' ); ?></p>
	  </td>
	</tr>
	<tr>
	  <th>
		<label for="rcp_settings[digistore_ipn_passphrase]"><?php _e( 'DigiStore IPN Passphrase', 'rcp' ); ?></label>
	  </th>
	  <td>
		<input type="text" class="regular-text" style="width: 300px;" name="rcp_settings[digistore_ipn_passphrase]" id="rcp_settings[digistore_ipn_passphrase]" value="<?php echo isset( $rcp_options['digistore_ipn_passphrase'] ) ? esc_attr( $rcp_options['digistore_ipn_passphrase'] ) : ''; ?>" />
		<p class="description"><?php _e( 'Enter your DigiStore IPN Passphrase.', 'rcp' ); ?></p>
	  </td>
	</tr>
  </table>
	<?php
}
add_action( 'rcp_payments_settings', 'crv_rcp_digistore_settings' );
