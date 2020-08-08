<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_teaser();
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

