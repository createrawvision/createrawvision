<?php

/**
 * For empty category archives show all sub-categories (also empty ones)
 */
add_action(
	'genesis_loop_else',
	function () {
		if ( ! is_admin() && is_main_query() && is_category() ) {

			$parent_category = get_queried_object();

			$child_categories = get_categories(
				array(
					'orderby'    => 'name',
					'parent'     => $parent_category->term_id,
					'hide_empty' => false,
				)
			);

			if ( count( $child_categories ) > 0 ) :

				// Don't show the "no content matches" message
				remove_action( 'genesis_loop_else', 'genesis_do_noposts' );

				foreach ( $child_categories as $category ) {
					$image_id = get_field( 'featured_image', $category );
					$link     = esc_url( get_category_link( $category->term_id ) );
					$title    = $category->name;

					require __DIR__ . '/templates/grid.php';
				}

		  endif;
		}
	},
	9
);

genesis();
