<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_restrict_content_pro();
};


/**
 * Install Restrict Content Pro
 * Make Settings and Membership Levels
 */
function deploy_restrict_content_pro() {
	WP_CLI::log( 'Installing and activating Restrict Content Pro' );
	$rcp_path = ABSPATH . '../deployment_data/restrict-content-pro.zip';
	run_wp_cli_command( "plugin install '$rcp_path' --activate --force", array( 'exit_error' => true ) );

	WP_CLI::log( 'Setting RCP Settings' );
	$rcp_settings         = array(
		'auto_renew'                           => '1',
		'currency'                             => 'EUR',
		'currency_position'                    => 'before',
		'gateways'                             => array( 'digistore' => '1' ),
		'email_template'                       => 'default',
		'from_name'                            => 'CreateRawVision',
		'from_email'                           => 'info@createrawvision.de',
		'admin_notice_emails'                  => 'info@createrawvision.de',
		'email_verification'                   => 'all',
		'verification_subject'                 => 'Bitte bestätige deine E-Mail Adresse',
		'verification_email'                   => 'Hallo %displayname%,

Bitte klicke hier, um deine E-Mail Adresse zu bestätigen:

%verificationlink%',
		'disable_active_email'                 => '1',
		'disable_free_email'                   => '1',
		'disable_trial_email'                  => '1',
		'disable_cancelled_email'              => '1',
		'disable_expired_email'                => '1',
		'disable_payment_received_email'       => '1',
		'disable_renewal_payment_failed_email' => '1',
		'disable_toolbar'                      => '1',
		'enable_terms'                         => '1',
		'terms_label'                          => 'AGBs zustimmen',
		'terms_link'                           => get_bloginfo( 'url' ) . '/agbs',
		'enable_privacy_policy'                => '1',
		'privacy_policy_label'                 => 'Datenschutzerklärung zustimmen',
		'privacy_policy_link'                  => get_privacy_policy_url(),
	);
	$current_rcp_settings = get_option( 'rcp_settings' );
	$new_rcp_settings     = wp_parse_args( $rcp_settings, $current_rcp_settings );
	update_option( 'rcp_settings', $new_rcp_settings );

	WP_CLI::warning( "RCP license key wasn't set. Add the license key manually." );

	WP_CLI::log( 'Creating membership levels' );
	$levels = array(
		array(
			'args' => array(
				'name'                => 'CreateRawVision Mitgliedschaft (monatlich)',
				'description'         => 'Erhalte noch heute Zugriff zu über 400 großartigen Rezepte, hilfreichen Tipps &amp; Tricks und einer wertschätzenden Gemeinschaft.',
				'duration'            => '1',
				'duration_unit'       => 'month',
				'trial_duration'      => '0',
				'trial_duration_unit' => 'day',
				'price'               => '9',
				'fee'                 => '0',
				'maximum_renewals'    => '0',
				'after_final_payment' => '',
				'list_order'          => '0',
				'level'               => '0',
				'status'              => 'active',
				'role'                => 'subscriber',
			),
			'meta' => array(
				'digistore_product_id' => '339809',
			),
		),
		array(
			'args' => array(
				'name'                => 'CreateRawVision Mitgliedschaft (jährlich)',
				'description'         => 'Erhalte noch heute Zugriff zu über 400 großartigen Rezepte, hilfreichen Tipps &amp; Tricks und einer wertschätzenden Gemeinschaft.',
				'duration'            => '1',
				'duration_unit'       => 'year',
				'trial_duration'      => '0',
				'trial_duration_unit' => 'day',
				'price'               => '90',
				'fee'                 => '-20',
				'maximum_renewals'    => '0',
				'after_final_payment' => '',
				'list_order'          => '0',
				'level'               => '0',
				'status'              => 'active',
				'role'                => 'subscriber',
			),
			'meta' => array(
				'digistore_product_id' => '301319',
			),
		),
		array(
			'args' => array(
				'name'                => 'CreateRawVision Mitgliedschaft (manuell)',
				'description'         => 'Erhalte noch heute Zugriff zu über 400 großartigen Rezepte, hilfreichen Tipps &amp; Tricks und einer wertschätzenden Gemeinschaft.',
				'duration'            => '1',
				'duration_unit'       => 'month',
				'trial_duration'      => '0',
				'trial_duration_unit' => 'day',
				'price'               => '0',
				'fee'                 => '0',
				'maximum_renewals'    => '1',
				'after_final_payment' => 'expire_immediately',
				'list_order'          => '0',
				'level'               => '1',
				'status'              => 'inactive',
				'role'                => 'subscriber',
			),
			'meta' => array(),
		),
	);

	global $rcp_levels_db;

	foreach ( $levels as $level ) {
		$level_id = $rcp_levels_db->insert( $level['args'] );

		foreach ( $level['meta'] as $meta_key => $meta_value ) {
			$rcp_levels_db->update_meta( $level_id, $meta_key, $meta_value );
		}
	}
}
