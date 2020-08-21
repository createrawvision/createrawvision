<?php
/**
 * Settings for RCP membership upgrades.
 */

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
