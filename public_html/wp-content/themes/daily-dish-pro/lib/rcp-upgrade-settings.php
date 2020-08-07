<?php
/**
 * Settings for RCP membership upgrades.
 */

/**
 * Upgrading Memberships only works from monthly to yearly.
 */
add_filter(
	'rcp_get_membership_upgrade_paths',
	function( $upgrade_paths, $membership_id, $membership ) {
		$level = ( new RCP_Levels() )->get_level( $membership->get_object_id() );
		if ( 'year' === $level->duration_unit ) {
			return array();
		}
		return $upgrade_paths;
	},
	10,
	3
);

/**
 * Enable upgrades only for the first 14 days after launch.
 *
 * @todo Enable upgrades, when user gets sent offer e-mail.
 */
add_filter(
	'rcp_can_upgrade_subscription',
	function( $can_upgrade, $user_id ) {
		// Don't enable upgrades, if they are disabled for this user.
		if ( ! $can_upgrade ) {
			return $can_upgrade;
		}

		return ! crv_is_before_membership_launch()
			&& crv_is_before_membership_launch( ( new DateTime() )->sub( new DateInterval( 'P14D' ) ) );
	},
	10,
	2
);

/**
 * Disable credit proration, when the last payment was the test phase payment.
 */
add_filter(
	'rcp_membership_disable_prorate_credit',
	function( $disable_credit, $membership ) {
		$recent_payments = $membership->get_payments(
			array(
				'number' => 1,
				'status' => 'complete',
			)
		);
		// No payments, no proration.
		if ( ! $recent_payments ) {
			return false;
		}

		$recent_payment = $recent_payments[0];
		return (float) $recent_payment->amount < 0.01;
	},
	10,
	2
);
