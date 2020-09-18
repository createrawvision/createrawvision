<?php

add_action( 'genesis_after_entry_content', 'crv_top_bookmarks' );

/**
 * Shows the top bookmarks from wp-bookmarks in the same style as archives.
 */
function crv_top_bookmarks() {

	$recipes_cat_id = 5869;

	$post_ids = get_posts(
		array(
			'posts_per_page' => 24,
			'fields'         => 'ids',
			'meta_key'       => '_wpb_post_bookmark_count',
			'order'          => 'DESC',
			'orderby'        => 'meta_value_num',
			'cat'            => $recipes_cat_id,
		)
	);

	echo '<div class="crv-grid">';

	// Get different title for admins.
	if ( current_user_can( 'manage_options' ) ) {
		$get_title = function( $post_id ) {
			return get_the_title( $post_id ) . ' (' . get_post_meta( $post_id, '_wpb_post_bookmark_count', true ) . ')';
		};
	} else {
		$get_title = 'get_the_title';
	}

	foreach ( $post_ids as $post_id ) {
		$title    = $get_title( $post_id );
		$link     = get_permalink( $post_id );
		$image_id = get_post_thumbnail_id( $post_id );

		require CHILD_DIR . '/templates/grid.php';
	}

	echo '</div>';
}

// Start the engine.
genesis();
