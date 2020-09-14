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

	return sprintf( '<a href="%s"><span class="nav-up-hint">Zurück zur Übersicht</span>%s</a>', esc_url( $category_link ), esc_html( $category->name ) );
}

/**
 * Get navigation link to previous post. Allow overwrite by custom field.
 */
function crv_get_navigation_previous() {
	return crv_get_navigation_adjacent( 'previous' );
}

/**
 * Get navigation link to next post. Allow overwrite by custom field.
 */
function crv_get_navigation_next() {
	return crv_get_navigation_adjacent( 'next' );
}

/**
 * Get the navigation link for previous or next post.
 *
 * @param "previous"|"next" $direction Select previous or next post.
 */
function crv_get_navigation_adjacent( $direction ) {
	$custom_link = get_field( "${direction}_post" );
	if ( $custom_link ) {
		return sprintf( '<a href="%s" rel="prev">%s</a>', $custom_link['url'], $custom_link['title'] );
	}
	$get_post_function = "get_${direction}_post_link";
	return $get_post_function( '%link', '%title', true );
}

/**
 * Add post navigation for posts in courses.
 */
add_action(
	'genesis_after_entry_content',
	function() {
		$previous = crv_get_navigation_previous();
		$up       = crv_get_navigation_up();
		$next     = crv_get_navigation_next();

		$navigation = _navigation_markup( $previous . $up . $next, 'post-navigation', __( 'Post navigation' ), __( 'Posts' ) );
		echo wp_kses_post( $navigation );
	}
);
