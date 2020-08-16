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

/**
 * Show a button to show unrestricted posts first on recipe category archives.
 */
add_action(
	'genesis_before_while',
	function() {
		$category       = get_queried_object();
		$recipes_cat_id = 5869;

		// Bail, if not child of recipes category.
		if ( ! cat_is_ancestor_of( $recipes_cat_id, $category ) ) {
			return;
		}

		if ( isset( $_GET['free'] ) && $_GET['free'] ) {
			echo '<form class="unrestricted-first"><button type="submit" name="free" value="0">Sortierung aufheben</button></form>';
		} else {
			echo '<form class="unrestricted-first"><button type="submit" name="free" value="1">Kostenfreie Rezepte zuerst anzeigen</button></form>';
		}
	}
);

genesis();
