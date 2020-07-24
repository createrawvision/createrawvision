<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_homepage_and_postlist();
};

/**
 * Publish homepage and move current home (postlist) to another page
 */
function deploy_homepage_and_postlist() {
	WP_CLI::log( 'Creating homepage' );
	$homepage_content = file_get_contents( ABSPATH . '../deployment_data/homepage.html' );
	if ( ! $homepage_content ) {
		WP_CLI::error( 'Failed to read homepage content' );
	}
	$homepage_id = wp_insert_post(
		array(
			'post_title'   => 'Startseite',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => $homepage_content,
		)
	);
	if ( ! $homepage_id ) {
		WP_CLI::error( 'Failed to create homepage' );
	}

	WP_CLI::log( 'Creating postlist' );
	$postlist_id = wp_insert_post(
		array(
			'post_title'   => 'Alle Beiträge',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		)
	);
	if ( ! $postlist_id ) {
		WP_CLI::error( 'Failed to create postlist' );
	}

	WP_CLI::log( 'Setting homepage and blog' );
	$options = array(
		array( 'show_on_front', 'page' ),
		array( 'page_on_front', $homepage_id ),
		array( 'page_for_posts', $postlist_id ),
	);
	foreach ( $options as list($key, $value) ) {
		update_option( $key, $value );
		if ( get_option( $key ) != $value ) {
			WP_CLI::error( "Failed to set option $key to $value" );
		}
	}
}
