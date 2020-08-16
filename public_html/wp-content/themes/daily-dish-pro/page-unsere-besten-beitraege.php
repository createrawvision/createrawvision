<?php

// Append the grid.
add_action(
	'genesis_after_entry_content',
	function() {
		$best_posts = get_post_meta( get_the_ID(), 'crv_best_posts', true );

		echo '<div class="crv-grid crv-grid--large">';

		foreach ( $best_posts as list('post_id' => $post_id, 'image_id' => $image_id) ) {
			$title = get_the_title( $post_id );
			$link  = get_permalink( $post_id );

			require __DIR__ . '/templates/grid.php';
		}

		echo '</div>';
	}
);

// Run the Genesis loop.
genesis();
