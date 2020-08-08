<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_support_plugin();
};


/**
 * Install and activate SupportCandy and tweak some settings
 */
function deploy_support_plugin() {
	WP_CLI::log( 'Installing supportcandy' );
	run_wp_cli_command( 'plugin install supportcandy --activate --force', array( 'exit_error' => true ) );

	WP_CLI::log( 'Creating support pages' );
	$contact_page    = get_page_by_path( 'kontaktformular' );
	$contact_page_id = wp_insert_post(
		array(
			'ID'           => $contact_page ? $contact_page->ID : 0, // Overwrite existing.
			'post_content' => '[wpsc_create_ticket]',
			'post_title'   => 'Kontaktformular',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);
	if ( is_wp_error( $contact_page_id ) ) {
		WP_CLI::warning( 'Failed to create support contact page: ', $contact_page_id->get_error_message() );
	}
	$tickets_page_id = wp_insert_post(
		array(
			'post_content' => '[supportcandy]',
			'post_title'   => 'Meine Supportanfragen',
			'post_name'    => 'support',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);
	if ( is_wp_error( $tickets_page_id ) ) {
		WP_CLI::warning( 'Failed to create support overview page: ', $tickets_page_id->get_error_message() );
		$tickets_page_id = 0;
	}

	WP_CLI::log( 'Hide ticket priority for customer ticket list and filter.' );
	$ticket_priority_term = get_term_by( 'slug', 'ticket_priority', 'wpsc_ticket_custom_fields' );
	if ( ! $ticket_priority_term ) {
		WP_CLI::warning( 'Failed to get ticket priority term' );
	} else {
		update_term_meta( $ticket_priority_term->term_id, 'wpsc_customer_ticket_list_status', '0' );
		update_term_meta( $ticket_priority_term->term_id, 'wpsc_allow_ticket_filter', '0' );
	}

	WP_CLI::log( 'Delete default ticket categories.' );
	$old_ticket_categories = get_terms(
		array(
			'taxonomy'   => 'wpsc_categories',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $old_ticket_categories ) ) {
		WP_CLI::warning( 'Failed to delete old ticket categories: ', $old_ticket_categories->get_error_message() );
	} else {
		foreach ( $old_ticket_categories as $ticket_category ) {
			wp_delete_term( $ticket_category->term_id, 'wpsc_categories' );
		}
	}

	WP_CLI::log( 'Create custom ticket categories.' );
	$ticket_categories = array(
		'Fragen zum Mitgliederbereich',
		'Technisches Problem',
		'Zahlungen & Rechnungen',
		'Fragen zu Zubehör',
		'Zusammenarbeit',
		'Verbesserungsvorschlag',
		'Rezeptwunsch',
		'Sonstiges',
	);
	foreach ( $ticket_categories as $key => $category_name ) {
		$term = wp_insert_term( $category_name, 'wpsc_categories' );
		if ( is_wp_error( $term ) ) {
			WP_CLI::warning( "Failed to insert ticket category term '$category_name': ", $term->get_error_message() );
			continue;
		}
		add_term_meta( $term['term_id'], 'wpsc_category_load_order', strval( $key + 1 ) );

		$wpsc_custom_category_localize = get_option( 'wpsc_custom_category_localize', array() );
		$wpsc_custom_category_localize[ 'custom_category_' . $term['term_id'] ] = $category_name;
		update_option( 'wpsc_custom_category_localize', $wpsc_custom_category_localize );

		if ( 0 === $key ) {
			$default_ticket_category_id = $term['term_id'];
		}
	}

	WP_CLI::log( 'Disable ticket closed notification.' );
	$email_template_ids = get_terms(
		array(
			'taxonomy'   => 'wpsc_en',
			'hide_empty' => false,
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'fields'     => 'ids',
		)
	);
	if ( is_wp_error( $email_template_ids ) ) {
		WP_CLI::warning( 'Failed to disable ticket notifications: ', $email_template_ids->get_error_message() );
	} else {
		$ticket_closed_term_id = $email_template_ids[3];
		wp_delete_term( $ticket_closed_term_id, 'wpsc_en' );
		$email_subject = get_option( 'wpsc_email_notification_subject', array() );
		unset( $email_subject[ 'email_subject_' . $ticket_closed_term_id ] );
		$email_body = get_option( 'wpsc_email_notification_body', array() );
		unset( $email_body[ 'email_body_' . $ticket_closed_term_id ] );
	}

	$options = array(
		'wpsc_default_ticket_category'    => $default_ticket_category_id,
		'wpsc_allow_guest_ticket'         => '1',
		'wpsc_set_in_gdpr'                => '1',
		'wpsc_hide_show_priority'         => '0', // == hide
		'wpsc_email_notification_subject' => $email_subject,
		'wpsc_email_notification_body'    => $email_body,
		'wpsc_support_page_id'            => $tickets_page_id,
		'wpsc_en_from_name'               => 'CreateRawVision',
		'wpsc_en_from_email'              => 'support@createrawvision.de',
		'wpsc_ticket_alice'               => 'Anfrage #',
		'wpsc_show_and_hide_filters'      => '0',
		'wpsc_allow_reply_confirmation'   => '0',
		'wpsc_reply_bcc_visibility'       => '0',
		'wpsc_custom_ticket_count'        => '100',
	);

	$default_ticket_priority = get_term_by( 'name', __( 'Medium', 'supportcandy' ), 'wpsc_priorities' );
	if ( ! $default_ticket_priority ) {
		WP_CLI::warning( 'Failed to get default ticket priority.' );
	} else {
		$options['wpsc_default_ticket_priority'] = $default_ticket_priority->term_id;
	}

	WP_CLI::log( 'Updating options' );
	foreach ( $options as $option_name => $option_value ) {
		update_option( $option_name, $option_value );
	}

	WP_CLI::success( 'supportcandy done' );
}
