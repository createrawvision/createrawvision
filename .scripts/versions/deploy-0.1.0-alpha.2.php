<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_teaser();
	deploy_landing_page();
	deploy_pages_for_templates();
	deploy_featured_image();
};


/**
 * Set the teaser data from ...
 * 1. /deployment_data/teaser-data.json
 * 2. If no image is set, select the first one
 */
function deploy_teaser() {
	WP_CLI::log( 'Setting teaser image for all member posts' );

	$teaser_json = file_get_contents( ABSPATH . '../deployment_data/teaser-data.json' );
	$teaser_data = $teaser_json ? json_decode( $teaser_json, $assoc = true ) : array();

	$field_keys = array(
		'custom_teaser' => acf_get_local_field( 'custom_teaser' )['key'],
		'teaser_text'   => acf_get_local_field( 'teaser_text' )['key'],
		'teaser_image'  => acf_get_local_field( 'teaser_image' )['key'],
	);

	$member_posts = get_posts(
		array(
			'numberposts'   => -1,
			'category_name' => 'member',
			'post_status'   => 'any',
		)
	);

	$progressbar = \WP_CLI\Utils\make_progress_bar( 'Creating teasers for all member posts', count( $member_posts ) );

	foreach ( $member_posts as $post ) {
		$data = array(
			$field_keys['custom_teaser'] => $teaser_data[ $post->ID ]['custom_teaser'] ?? null,
			$field_keys['teaser_text']   => $teaser_data[ $post->ID ]['teaser_text'] ?? null,
		);
		if ( isset( $teaser_data[ $post->ID ]['teaser_image_id'] ) ) {
			$data[ $field_keys['teaser_image'] ] = $teaser_data[ $post->ID ]['teaser_image_id'];
		} else {
			preg_match( '/<img.+?class=[\'"].*?wp-image-(\d*).*?[\'"].*?>/i', $post->post_content, $matches );
			if ( count( $matches ) == 0 ) {
				WP_CLI::warning( "Couldn't find first image in post $post->post_title" );
			} else {
				$first_image_id                      = $matches[1];
				$data[ $field_keys['teaser_image'] ] = $first_image_id;
			}
		}
		$success = acf_save_post( $post->ID, $data );

		if ( ! $success ) {
			WP_CLI::warning( "Couldn't add teaser data for post '$post->post_title' ($post->ID). Skipping..." );
		}

		$progressbar->tick();
	}
	$progressbar->finish();
}


/**
 * Creates the landing page for membership
 */
function deploy_landing_page() {
	WP_CLI::log( 'Creating landing page' );

	wp_insert_post(
		array(
			'post_content'  => '',
			'post_title'    => 'Member Landing Page',
			'post_name'     => 'member-landing',
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'page_template' => 'landing',
		)
	);
}


/**
 * Creates empty pages for template files to use
 *
 * Pages:
 * * dashboard
 * * login
 */
function deploy_pages_for_templates() {
	 WP_CLI::log( 'Creating dashboard page' );

	wp_insert_post(
		array(
			'post_content' => '',
			'post_title'   => 'Member Dashboard',
			'post_name'    => 'dashboard',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);

	WP_CLI::log( 'Creating login page' );

	wp_insert_post(
		array(
			'post_content' => '[login_form redirect="/dashboard"]',
			'post_title'   => 'Login',
			'post_name'    => 'login',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);

	WP_CLI::log( 'Creating search page' );

	wp_insert_post(
		array(
			'post_content' => '',
			'post_title'   => 'Erweiterte Rezept-Suche',
			'post_name'    => 'suche',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);
}


/**
 * Sets the featured images from `deployment_data/featured-images.json`
 */
function deploy_featured_image() {
	$featured_image_json = file_get_contents( ABSPATH . '../deployment_data/featured-images.json' );
	$featured_image_data = $featured_image_json ? json_decode( $featured_image_json, $assoc = true ) : array();

	$progressbar = \WP_CLI\Utils\make_progress_bar( 'Setting featured images from json data', count( $featured_image_data ) );

	foreach ( $featured_image_data as $post_id => $thumbnail_id ) {
		$success = set_post_thumbnail( $post_id, $thumbnail_id );

		if ( ! $success ) {
			WP_CLI::warning( "Couldn't set thumbnail for post $post_id" );
		}

		$progressbar->tick();
	}

	$progressbar->finish();
}


/**
 * Publishes all private posts from category member excluding the posts in `deployment_data/private-posts.json`
 */
function deploy_private_posts() {
	$excluded_posts_json = file_get_contents( ABSPATH . '../deployment_data/private-posts.json' );
	$excluded_posts      = $excluded_posts_json ? json_decode( $excluded_posts_json, $assoc = true ) : array();
	$excluded_post_ids   = array_map(
		function ( $post ) {
			return $post['id'];
		},
		$excluded_posts
	);

	$private_member_post_ids = get_posts(
		array(
			'numberposts' => -1,
			'category'    => get_category_by_slug( 'member' )->term_id,
			'post_status' => 'private',
			'fields'      => 'ids',
			'exclude'     => $excluded_post_ids,
		)
	);

	$progressbar = \WP_CLI\Utils\make_progress_bar( 'Publishing private posts', count( $private_member_post_ids ) );

	foreach ( $private_member_post_ids as $post_id ) {
		$success = 0 !== wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => 'publish',
			)
		);

		if ( ! $success ) {
			WP_CLI::warning( "Failed to update post {$post_id}" );
		}
		$progressbar->tick();
	}
	$progressbar->finish();
}


/**
 * Activates wp-bookmark plugin and sets options
 */
function deploy_bookmark_plugin() {
	 WP_CLI::log( 'Activating and setting up wp-bookmarks' );

	// Actiavte plugin.
	run_wp_cli_command( 'plugin activate wp-bookmarks', array( 'exit_error' => true ) );

	// Set plugin options.
	$new_option = array(
		'wp_bookmark_popup_type'         => '1',
		'wpb_show_sharebutton'           => '0',
		'width'                          => '',
		'align'                          => 'left',
		'inline'                         => '0',
		'no_top_margin'                  => '0',
		'no_bottom_margin'               => '0',
		'pct_gap'                        => '5',
		'px_gap'                         => '20',
		'widgetized'                     => '1',
		'bookmark_hearticon'             => '0',
		'wpb_bookmark_category'          => '0',
		'wpb_add_collections'            => '1',
		'remove_bookmark'                => 'Lesezeichen entfernen',
		'dialog_bookmarked'              => 'Lesezeichen wurde hinzugefügt',
		'dialog_unbookmarked'            => 'Lesezeichen wurde entfernt',
		'default_collection'             => 'Standardkategorie',
		'add_to_collection'              => 'Zur Kategorie hinzufügen',
		'new_collection'                 => 'Neue Kategorie',
		'new_collection_placeholder'     => 'Name der Kategorie',
		'add_new_collection'             => 'Neue Kategorie erstellen',
		'bookmark_category'              => 'Lesezeichen für Kategorie erstellen',
		'remove_bookmark_category'       => 'Lesezeichen für Kategorie entfernen',
		'allow_multiple_bookmarks'       => '1',
		'auto_bookmark'                  => '0',
		'include_post_types'             =>
		array(
			0 => 'post',
			1 => 'page',
		),
		'exclude_ids'                    => '',
		'bookmarks_envato_purchase_code' => '',
		'wpb_show_users_avatar'          => '0',
		'wpb_new_collection_limit'       => '100',
		'wpb_bookmarks_limit'            => '100',
		'exclude_post_types'             => '',
	);

	$option = get_option( 'wpb', $new_option );
	$option = wp_parse_args( $new_option, $option );
	update_option( 'wpb', $option );

	WP_CLI::success( 'wp-bookmarks done' );
}


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

	$default_ticket_priority = get_term_by( 'name', __( 'Normal', 'supportcandy' ), 'wpsc_priorities' );
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
