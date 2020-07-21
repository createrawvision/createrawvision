<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_private_pages_and_member_posts();
};


/**
 * Publishes all private pages and posts from category member excluding the posts in `deployment_data/private-posts.json`
 */
function deploy_private_pages_and_member_posts() {
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

	$private_page_ids = get_posts(
		array(
			'numberposts' => -1,
			'post_status' => 'private',
			'fields'      => 'ids',
		)
	);

	$private_ids = array_merge( $private_member_post_ids, $private_page_ids );

	$progressbar = \WP_CLI\Utils\make_progress_bar( 'Publishing private posts', count( $private_ids ) );

	foreach ( $private_ids as $post_id ) {
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
