<?php

/**
 * Navigation for posts.
 */
class CrvPostNavigation {
	/**
	 * Get navigation link to post category.
	 */
	public function get_navigation_up() {
		$category_id = crv_get_primary_taxonomy_id( get_the_ID() );
		if ( ! $category_id ) {
			return '';
		}
		$category_name = get_cat_name( $category_id );
		$category_link = get_category_link( $category_id );

		return sprintf( '<a href="%s"><span class="nav-up-hint">Zurück zur Übersicht</span>%s</a>', esc_url( $category_link ), esc_html( $category_name ) );
	}

	/**
	 * Get navigation link to previous post. Allow overwrite by custom field.
	 */
	public function get_navigation_previous() {
		return $this->get_navigation_adjacent( 'previous' );
	}

	/**
	 * Get navigation link to next post. Allow overwrite by custom field.
	 */
	public function get_navigation_next() {
		return $this->get_navigation_adjacent( 'next' );
	}

	/**
	 * Get the navigation link for previous or next post.
	 *
	 * @param "previous"|"next" $direction Select previous or next post.
	 */
	private function get_navigation_adjacent( $direction ) {
		$custom_link = get_field( "${direction}_post" );
		$rel         = 'next' === $direction ? 'next' : 'prev';
		if ( $custom_link ) {
			return sprintf( '<a href="%s" rel="%s">%s</a>', $custom_link['url'], $rel, $custom_link['title'] );
		}
		$get_post_function = "get_${direction}_post_link";
		return $get_post_function( '%link', '%title', true );
	}
}
