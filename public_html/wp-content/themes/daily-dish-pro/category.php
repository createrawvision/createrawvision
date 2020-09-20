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

			$search_input     = sanitize_text_field( wp_unslash( $_GET['s_cat'] ?? '' ) );
			$child_categories = crv_filter_categories( $child_categories, $search_input );

			if ( count( $child_categories ) > 0 ) :

				// Don't show the "no content matches" message
				remove_action( 'genesis_loop_else', 'genesis_do_noposts' );
				do_action( 'genesis_before_while' );

				foreach ( $child_categories as $category ) {
					$image_id      = get_field( 'featured_image', $category );
					$category_link = get_category_link( $category->term_id );
					$raw_link      = $search_input ? add_query_arg( 's_cat', $search_input, $category_link ) : $category_link;
					$link          = esc_url( $raw_link );
					$title         = $category->name .
						'<span class="cat-search-info' . ( $category->direct_match ? ' direct-match' : '' ) . '">' . $category->matches . ' Treffer</span>';

					require __DIR__ . '/templates/grid.php';
				}

			endif;
		}
	},
	9
);


/**
 * Show forms before loop.
 */
add_action(
	'genesis_before_while',
	function() {
		echo '<div class="crv-archive-forms">';
		crv_show_category_filter_form();
		crv_show_unrestricted_posts_first_form();
		echo '</div>';
	}
);


/**
 * Filter categories to match search.
 */
function crv_filter_categories( $categories, $search_input ) {
	// No search input, nothing to do.
	if ( ! $search_input ) {
		return $categories;
	}

	$category_ids            = array_column( $categories, 'term_id' );
	$matched_category_counts = array();

	// Search for posts in categories.
	$matched_posts = get_posts(
		array(
			'numberposts' => -1,
			'category'    => join( ',', $category_ids ),
			's'           => $search_input,
		)
	);

	// Count how often each category got matched.
	foreach ( $matched_posts as $matched_post ) {
		$matched_categories = get_the_terms( $matched_post->ID, 'category' );
		if ( is_array( $matched_categories ) ) {
			foreach ( $matched_categories as $matched_category ) {
				$current_count = $matched_category_counts[ $matched_category->term_id ] ?? 0;
				$matched_category_counts[ $matched_category->term_id ] = $current_count + 1;
			}
		}
	}

	// Add the results to the parent categories.
	foreach ( $matched_category_counts as $category_id => $matched_category_count ) {
		$ancestor_category_ids = get_ancestors( $category_id, 'category', 'taxonomy' );
		foreach ( $ancestor_category_ids as $category_id ) {
			$current_count                           = $matched_category_counts[ $category_id ] ?? 0;
			$matched_category_counts[ $category_id ] = $current_count + $matched_category_count;
		}
	}

	// Filter only categories to match in the first place.
	$filtered_category_counts = array();
	foreach ( $categories as $category ) {
		$count = $matched_category_counts[ $category->term_id ] ?? 0;
		if ( ! $count ) {
			continue;
		}
		$filtered_category_counts[ $category->term_id ] = $count;
	}
	arsort( $filtered_category_counts );
	$sorted_category_ids = array_keys( $filtered_category_counts );

	// Search for categories.
	$category_ids_matched_by_name = get_categories(
		array(
			'search'  => $search_input,
			'include' => $category_ids,
			'orderby' => 'include',
			'fields'  => 'ids',
		)
	);

	// Sort categories by category search first, then number of hits in post search.
	$matched_category_ids = array_merge( $category_ids_matched_by_name, array_diff( $sorted_category_ids, $category_ids_matched_by_name ) );

	$categories = get_categories(
		array(
			'include' => $matched_category_ids,
			'orderby' => 'include',
		)
	);

	// Add a number of matches and match by name to category object.
	$categories = array_map(
		function( $category ) use ( $filtered_category_counts, $category_ids_matched_by_name ) {
			$category->matches      = $filtered_category_counts[ $category->term_id ];
			$category->direct_match = in_array( $category->term_id, $category_ids_matched_by_name, true );
			return $category;
		},
		$categories
	);

	return $categories;
}

/**
 * Show a form to filter the categories.
 */
function crv_show_category_filter_form() {
	?>
	<form class="archive-filter">
		<label>
			Elemente filtern
			<input type="search" name="s_cat" value="<?php echo esc_attr( $_GET['s_cat'] ); ?>">
		</label>
		<button type="submit">Filtern</button>
	</form>
	<?php
}

/**
 * Show a button to show unrestricted posts first on recipe category archives.
 */
function crv_show_unrestricted_posts_first_form() {
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

genesis();
