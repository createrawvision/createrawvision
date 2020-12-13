<?php

require __DIR__ . '/MailChimp.php';

use DrewM\MailChimp\MailChimp;

// Add new members to Mailchimp only when their e-mail-address is verified.
add_action( 'rcp_customer_post_verify_email', 'crv_subscribe_verified_customer_to_mailchimp', 10, 2 );

// When a members e-mail or name changes, update it in Mailchimp.
add_action(
	'rcp_user_profile_updated',
	function ( $user_id, $userdata, $old_userdata ) {
		if ( crv_rcp_skip_mailchimp( $user_id ) ) {
			return;
		}
		crv_add_user_to_mailchimp( $userdata['user_email'], $userdata['first_name'], $userdata['last_name'], $old_userdata->user_email );
	},
	10,
	3
);

// When a member's status changes, update the merge field in Mailchimp.
add_action( 'rcp_transition_membership_status', 'crv_update_rcp_status_mailchimp', 10, 3 );


/**
 * Subscribes a customer with verified e-mail-address to Mailchimp list
 */
function crv_subscribe_verified_customer_to_mailchimp( $customer_id, $customer ) {
	$customer = rcp_get_customer( $customer_id );

	// Bail, if e-mail is not verified
	if ( $customer->is_pending_verification() ) {
		return;
	}

	$user_id    = $customer->get_user_id();
	$user       = get_user_by( 'id', $user_id );
	$membership = rcp_get_membership_by( 'customer_id', $customer_id );

	if ( crv_rcp_skip_mailchimp( $user_id ) ) {
		return;
	}

	crv_add_user_to_mailchimp( $user->user_email, $user->first_name, $user->last_name );

	crv_update_rcp_status_mailchimp( '', $membership->get_status(), $membership->get_id() );
}

/**
 * Adds or updates contact in Mailchimp
 */
function crv_add_user_to_mailchimp( $new_email, $first_name, $last_name, $old_email = '' ) {
	global $rcp_options;

	try {
		$MailChimp       = new MailChimp( $rcp_options['mailchimp_api_key'] );
		$list_id         = 'a04c23f499'; // List "Createrawvision"
		$subscriber_hash = $MailChimp->subscriberHash( $old_email ?: $new_email );

		$MailChimp->put(
			"lists/{$list_id}/members/{$subscriber_hash}",
			array(
				'email_address' => $new_email,
				'status_if_new' => 'subscribed',
				'merge_fields'  => array(
					'FNAME' => $first_name,
					'LNAME' => $last_name,
				),
			)
		);

		if ( ! $MailChimp->success() ) {
			throw new Exception( 'Failed to add member to Mailchimp list: ' . $MailChimp->getLastError() );
		}
	} catch ( Exception $e ) {
		rcp_log( $e->getMessage() );
	}
}

/**
 * Updates the mailchimp RCP status merge field.
 */
function crv_update_rcp_status_mailchimp( $old_status, $new_status, $membership_id ) {
	global $rcp_options;

	try {
		$membership = rcp_get_membership( $membership_id );
		if ( $membership->was_upgraded() || $membership->is_disabled() ) {
			// Fix the status in MailChimp going wild, since on upgrades
			// the membership gets disabled and then cancelled from the payment gateway.
			return;
		}
		$user_id = $membership->get_user_id();
		$user    = get_user_by( 'id', $user_id );

		if ( crv_rcp_skip_mailchimp( $user_id ) ) {
			return;
		}

		$MailChimp                     = new MailChimp( $rcp_options['mailchimp_api_key'] );
		$list_id                       = 'a04c23f499'; // List "Createrawvision"
		$member_tag_segment_id         = '508621'; // Tag "member"
		$member_yearly_tag_segement_id = '511654'; // Tag "member-yearly"
		$subscriber_hash               = $MailChimp->subscriberHash( $user->user_email );

		if ( $old_status === 'active' && $new_status === 'expired' ) {
			$rcp_status = 'expired-suddenly';
		} elseif ( $new_status === 'active' && crv_is_before_membership_launch() ) {
			$rcp_status = 'active-presale';
		} else {
			$rcp_status = $new_status;
		}

		$MailChimp->patch(
			"lists/{$list_id}/members/{$subscriber_hash}",
			array(
				'merge_fields' => array(
					'RCP_STATUS' => $rcp_status,
				),
			)
		);

		if ( ! $MailChimp->success() ) {
			throw new Exception( 'Failed to update RCP status in Mailchimp: ' . $MailChimp->getLastError() );
		}

		// Tag as "member" or "member-yearly" on active/cancelled membership, untag otherwise
		$level = ( new RCP_Levels() )->get_level( $membership->get_object_id() );
		if ( 'year' === $level->duration_unit ) {
			$tag_segment_id   = $member_yearly_tag_segement_id;
			$untag_segment_id = $member_tag_segment_id;
		} else {
			$tag_segment_id   = $member_tag_segment_id;
			$untag_segment_id = $member_yearly_tag_segement_id;
		}
		$tag_url   = "lists/{$list_id}/segments/{$tag_segment_id}/members";
		$untag_url = "lists/{$list_id}/segments/{$untag_segment_id}/members";
		$tag_data  = array( 'email_address' => $user->user_email );

		$MailChimp->delete( $untag_url, $tag_data );
		if ( in_array( $new_status, array( 'active', 'cancelled' ) ) ) {
			$MailChimp->post( $tag_url, $tag_data );
		} else {
			$MailChimp->delete( $tag_url, $tag_data );
		}

		if ( ! $MailChimp->success() ) {
			throw new Exception( 'Failed to update member tag in Mailchimp: ' . $MailChimp->getLastError() );
		}
	} catch ( Exception $e ) {
		rcp_log( $e->getMessage() );
	}
}


/**
 * Determines, when not to hit the Mailchimp API.
 * Currently when a user only has manual levels.
 */
function crv_rcp_skip_mailchimp( $user_id, $manual_levels = array( 3 ) ) {

	$customer = rcp_get_customer_by_user_id( $user_id );
	if ( ! $customer ) {
		return true;
	}

	$memberships = $customer->get_memberships();
	if ( ! $memberships ) {
		return true;
	}

	$level_ids = array_unique(
		array_map(
			function( $membership ) {
				return $membership->get_object_id();
			},
			$memberships
		)
	);

	$only_manual_levels = array_reduce(
		$level_ids,
		function( $only_manual_levels, $level_id ) use ( $manual_levels ) {
			return $only_manual_levels && in_array( $level_id, $manual_levels );
		},
		true
	);

	return $only_manual_levels;
}


// Add setting for MailChimp API key
add_action(
	'rcp_email_settings',
	function ( $rcp_options ) { ?>
	<table class="form-table">
		<tr valign="top">
			<th colspan=2>
				<h3><?php _e( 'MailChimp Settings', 'rcp' ); ?></h3>
			</th>
		</tr>
		<tr>
			<th>
				<label for="rcp_settings[mailchimp_api_key]"><?php _e( 'MailChimp API Key', 'rcp' ); ?></label>
			</th>
			<td>
				<input type="text" class="regular-text" style="width: 300px;" name="rcp_settings[mailchimp_api_key]" id="rcp_settings[mailchimp_api_key]" value="<?php echo isset( $rcp_options['mailchimp_api_key'] ) ? esc_attr( $rcp_options['mailchimp_api_key'] ) : ''; ?>" />
				<p class="description"><?php _e( 'Enter your MailChimp API key.', 'rcp' ); ?></p>
			</td>
		</tr>
	</table>
		<?php
	}
);
