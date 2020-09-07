<?php
/**
 * Adds navigation to courses.
 */

/**
 * Gets the primary term in the given taxonomy set via Yoast.
 * Falls back to first term, if there is none.
 *
 * @link https://wordpress.stackexchange.com/a/315577
 */
function crv_get_primary_taxonomy_id( $post_id, $taxonomy ) {
	$prm_term = '';
	if ( class_exists( 'WPSEO_Primary_Term' ) ) {
		$wpseo_primary_term = new WPSEO_Primary_Term( $taxonomy, $post_id );
		$prm_term           = $wpseo_primary_term->get_primary_term();
	}
	if ( ! is_object( $wpseo_primary_term ) || empty( $prm_term ) ) {
		$term = wp_get_post_terms( $post_id, $taxonomy );
		if ( isset( $term ) && ! empty( $term ) ) {
			return $term[0]->term_id;
		} else {
			return '';
		}
	}
	return $wpseo_primary_term->get_primary_term();
}

/**
 * Get navigation link to post category.
 */
function crv_get_navigation_up() {
	$category_id = crv_get_primary_taxonomy_id( get_the_ID(), 'category' );
	if ( ! $category_id ) {
		return '';
	}
	$category      = get_category( $category_id );
	$category_link = get_category_link( $category_id );

	return sprintf( '<div class="nav-up"><a href="%s">%s</a></div>', esc_url( $category_link ), esc_html( $category->name ) );
}

/**
 * Add post navigation for posts in courses.
 */
add_action(
	'genesis_after_entry_content',
	function() {
		$previous = get_previous_post_link(
			'<div class="nav-previous">%link</div>',
			'Zurück zu "%title"',
			true
		);
		$next     = get_next_post_link(
			'<div class="nav-next">%link</div>',
			'Weiter zu "%title"',
			true
		);
		$up       = crv_get_navigation_up();

		$navigation = _navigation_markup( $previous . $up . $next, 'post-navigation', __( 'Post navigation' ), __( 'Posts' ) );
		echo wp_kses_post( $navigation );
	}
);
