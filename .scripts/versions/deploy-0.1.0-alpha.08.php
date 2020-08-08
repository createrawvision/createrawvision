<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_bookmark_plugin();
};


/**
 * Activates wp-bookmark plugin and sets options
 */
function deploy_bookmark_plugin() {
	WP_CLI::log( 'Activating and setting up wp-bookmarks' );

	run_wp_cli_command( 'plugin activate wp-bookmarks', array( 'exit_error' => true ) );

	WP_CLI::log( 'Creating bookmarks page.' );
	$bookmarks_page_id = wp_insert_post(
		array(
			'post_content' => '[collections]',
			'post_title'   => 'Meine Lesezeichen',
			'post_name'    => 'lesezeichen',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);
	if ( is_wp_error( $bookmarks_page_id ) ) {
		WP_CLI::warning( 'Failed to create bookmarks page: ', $bookmarks_page_id->get_error_message() );
	}

	WP_CLI::log( 'Tweaking plugin settings' );
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
		'include_post_types'             => array( 0 => 'post' ),
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
