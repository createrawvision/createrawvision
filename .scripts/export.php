<?php

/**
 * Exports edits from the current environment
 *
 * Execute with WP-CLI (and add `--user=Josef` to have enough capabilites)!
 *
 * `wp eval-file .scripts/export.php [function_names...] --user=Josef`
 * `function_names` calls the functions with names `export_{function_name}`
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	echo 'WP_CLI not defined', PHP_EOL;
	exit( 1 );
}

if ( ! current_user_can( 'manage_options' ) ) {
	echo 'Insufficient capabilites. Make sure to run the script with admin capabilites (e.g. --user=<admin>).', PHP_EOL;
	exit( 1 );
}

require_once __DIR__ . '/wp-cli-utils.php';

// Avoid the output buffer
ob_end_flush();
ob_implicit_flush();

// If arguments are provided, call the given functions
// Else call all functions
if ( empty( $args ) ) {
	export_teasers();
	export_featured_images();
} else {
	foreach ( $args as $arg ) {
		$function_name = "export_{$arg}";
		if ( ! function_exists( $function_name ) ) {
			WP_CLI::warning( "function {$functionname} does not exist" );
			continue;
		}
		call_user_func( $function_name );
	}
}


/*
   ###############
   #             #
   #  FUNCTIONS  #
   #             #
   ###############
*/

/**
 * Save ACF local fields for teaser text and image for member posts in deployment_data/teaser-data-<timestamp>
 */
function export_teasers() {
	 WP_CLI::log( 'Exporting teasers data' );

	$member_post_ids = get_posts(
		array(
			'numberposts'   => -1,
			'category_name' => 'member',
			'post_status'   => 'any',
			'fields'        => 'ids',
		)
	);

	$teaser_data = array();

	foreach ( $member_post_ids as $post_id ) {
		$teaser_data[ $post_id ] = array_filter(
			array(
				'custom_teaser'   => get_field( 'custom_teaser', $post_id ),
				'teaser_text'     => get_field( 'teaser_text', $post_id ),
				'teaser_image_id' => get_field( 'teaser_image', $post_id )['id'],
			),
			function ( $v ) {
				return ! is_null( $v );
			}
		);
	}

	$path = __DIR__ . '/../deployment_data/teaser-data.json';
	merge_and_write_to_json( $path, $teaser_data );
}


/**
 * Exports all featured images from non-member recipes into `deployment_data/featured-images.php`
 */
function export_featured_images() {
	 WP_CLI::log( 'Exporting featured images data' );

	$free_recipe_category_ids = get_categories(
		array(
			'parent' => get_category_by_slug( 'vegane-rezepte' )->term_id,
			'fields' => 'ids',
		)
	);

	$free_recipe_post_ids = get_posts(
		array(
			'numberposts' => -1,
			'category'    => implode( ',', $free_recipe_category_ids ),
			'post_status' => 'any',
			'fields'      => 'ids',
		)
	);

	$featured_image_data = array();

	foreach ( $free_recipe_post_ids as $post_id ) {
		$featured_image_data[ $post_id ] = get_post_thumbnail_id( $post_id );
	}

	$path = __DIR__ . '/../deployment_data/featured-images.json';
	merge_and_write_to_json( $path, $featured_image_data );
}


/**
 * Writes data into file after merging it with existing values (with the array union operator)
 */
function merge_and_write_to_json( $path, $data ) {
	if ( file_exists( $path ) ) {
		$old_data = json_decode( file_get_contents( $path ), $assoc = true );
		if ( is_array( $old_data ) ) {
			$data = $data + $old_data;
		}
	}
	file_put_contents( $path, json_encode( $data, JSON_PRETTY_PRINT ) );
}
