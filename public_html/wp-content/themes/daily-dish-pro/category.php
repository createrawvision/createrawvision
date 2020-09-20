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
	$query_args = array(
		'nopaging' => true,
		'cat'      => join( ',', $category_ids ),
		's'        => $search_input,
	);
	if ( function_exists( 'relevanssi_do_query' ) ) {
		$matched_posts_query = new WP_Query(); // Don't execute query.
		$matched_posts_query->parse_query( $query_args );
		$matched_posts = relevanssi_do_query( $matched_posts_query );
	} else {
		$matched_posts = get_posts( $query_args );
	}

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
		function ( $category ) use ( $filtered_category_counts, $category_ids_matched_by_name ) {
			$category->matches      = $filtered_category_counts[ $category->term_id ];
			$category->direct_match = in_array( $category->term_id, $category_ids_matched_by_name, true );
			return $category;
		},
		$categories
	);

	return $categories;
}


/**
 * Show forms before loop.
 */
add_action(
	'genesis_before_while',
	function () {
		global $wp;
		$search_input                  = sanitize_text_field( wp_unslash( $_GET['s_cat'] ?? '' ) );
		$show_unrestricted_posts_first = isset( $_GET['free'] ) && $_GET['free'];
		?>
		<div class="crv-archive-search">
			<h2>Diese Kategorie Durchsuchen</h2>
			<?php if ( $search_input ) : ?>
				<form action="<?php echo esc_url( home_url( $wp->request ) ); ?>" class="crv-current-archive-search">
					<span class="current-cat-search-hint">Aktueller Suchbegriff </span>
					<span class="current-cat-search-input"><?php echo esc_html( $search_input ); ?></span>
					<input type="submit" value="Suche löschen">
				</form>
			<?php endif; ?>
			<form class="crv-archive-search-form">
				<input type="search" name="s_cat" value="<?php echo esc_attr( $search_input ); ?>" placeholder="Suchbegriff" aria-label="Suchbegriff">
				<?php
				// Show only in leaf category when child of recipes category.
				$recipes_category_id = 5869;
				$category            = get_queried_object();
				if ( $category->count && cat_is_ancestor_of( $recipes_category_id, $category ) ) :
					?>
					<div class="crv-unrestricted-posts-first">
						<input type="checkbox" id="crv-unrestricted-posts-first" name="free" <?php echo $show_unrestricted_posts_first ? 'checked' : ''; ?>>
						<label for="crv-unrestricted-posts-first">Kostenfreie Rezepte zuerst anzeigen</label>
					</div>
				<?php endif; ?>
				<button type="submit">In dieser Kategorie suchen</button>
			</form>
		</div>
		<?php
	}
);

genesis();
