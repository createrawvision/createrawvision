<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_featured_image();
};


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
