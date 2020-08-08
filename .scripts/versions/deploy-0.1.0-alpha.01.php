<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_category_featured_images();
};

/**
 * Install Advanced Custom Fields
 * Remove posts from categories with child categories
 * Setting category image data from /deployment_data/category-images.json
 */
function deploy_category_featured_images() {
	WP_CLI::log( 'Installing and activating Advanced Custom Fields' );
	run_wp_cli_command( 'plugin install advanced-custom-fields --activate --force', array( 'exit_error' => true ) );

	WP_CLI::log( 'Removing categories from posts, when category has child categories' );

	$childless_category_ids = get_categories(
		array(
			'childless'  => true,
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);
	$all_category_ids       = get_categories(
		array(
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);
	$parent_category_ids    = array_values(
		array_diff(
			$all_category_ids,
			$childless_category_ids
		)
	);

	$post_ids_to_edit = get_posts(
		array(
			'category__in'   => $parent_category_ids,
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'post_status'    => 'any',
		)
	);

	foreach ( $post_ids_to_edit as $post_id ) {
		wp_remove_object_terms( $post_id, $parent_category_ids, 'category' );
	}
	WP_CLI::log( 'Removed parent categories from following posts: ' . ( implode( ',', $post_ids_to_edit ) ?: '(none)' ) );

	WP_CLI::log( 'Setting category featured image from JSON data' );
	$category_images_json = file_get_contents( ABSPATH . '../deployment_data/category-images.json' );
	$category_images      = json_decode( $category_images_json, $assoc = true );
	foreach ( $category_images as list(
		'term_name'   => $term_name,
		'term_id'     => $term_id,
		'image_title' => $image_title,
	) ) {

		// Get the image_id by title, skip if not found
		$images = get_posts(
			array(
				'post_type'   => 'attachment',
				'title'       => $image_title,
				'post_status' => null,
				'numberposts' => 1,
			)
		);
		if ( ! $images ) {
			WP_CLI::warning( "Couldn't find image with title ${image_title}. Skipping..." );
			continue;
		}
		$image_id = $images[0]->ID;

		// get term_id by id, then by name, skip if not found
		$term = get_term_by( 'id', $term_id, 'category' );
		if ( ! $term ) {
			WP_CLI::warning( "Couldn't find term with id ${term_id}. Skipping..." );
			$term = get_term_by( 'name', $term_name, 'category' );
		}
		if ( ! $term ) {
			WP_CLI::warning( "Couldn't find term with name ${term_name}. Trying id..." );
			continue;
		}
		$term_id = $term->term_id;

		$success = acf_save_post( 'term_' . $term_id, array( 'field_1' => $image_id ) );

		if ( ! $success ) {
			WP_CLI::warning( "Couldn't add image $image_title to term $term->name. Skipping..." );
		}
	}
}
