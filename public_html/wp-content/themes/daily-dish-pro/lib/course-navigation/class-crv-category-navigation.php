<?php
/**
 * Navigation for categories.
 */
class CrvCategoryNavigation {
	/**
	 * Create new instance for navigating in this category.
	 *
	 * @param string $category_id Category ID where to display navigation.
	 */
	public function __construct( $category_id ) {
		$this->category_id = $category_id;
		$this->category    = get_category( $this->category_id );

		$this->get_adjacent_categories();
	}

	/**
	 * Get navigation link to parent category.
	 */
	public function get_navigation_up() {
		if ( ! $this->parent_category_id ) {
			return;
		}
		$category_name = get_cat_name( $this->parent_category_id );
		$category_link = get_category_link( $this->parent_category_id );

		return sprintf( '<a href="%s"><span class="nav-up-hint">Zurück zur Übersicht</span>%s</a>', esc_url( $category_link ), esc_html( $category_name ) );
	}

	/**
	 * Get navigation link to previous category. Allow overwrite by custom field.
	 */
	public function get_navigation_previous() {
		return $this->get_navigation_adjacent( 'previous' );
	}

	/**
	 * Get navigation link to next category. Allow overwrite by custom field.
	 */
	public function get_navigation_next() {
		return $this->get_navigation_adjacent( 'next' );
	}

	/**
	 * Get the navigation link for previous or next category. Allow overwrite by custom field.
	 *
	 * @param "previous"|"next" $direction Select previous or next category.
	 */
	private function get_navigation_adjacent( $direction ) {
		$category_id = get_field( "${direction}_category", $this->category );
		if ( ! $category_id ) {
			$category_id = $this->{"${direction}_category_id"};
		}
		if ( ! $category_id ) {
			return;
		}
		$rel           = 'next' === $direction ? 'next' : 'prev';
		$category_name = get_cat_name( $category_id );
		$category_link = get_category_link( $category_id );
		return sprintf( '<a href="%s" rel="%s">%s</a>', esc_url( $category_link ), $rel, esc_html( $category_name ) );
	}

	/**
	 * Calculate the previous, next and parent categories.
	 */
	private function get_adjacent_categories() {
		$this->parent_category_id = $this->category->parent;

		$categories = get_categories(
			array(
				'parent'     => $this->parent_category_id,
				'hide_empty' => false,
				'order'      => 'ASC',
				'orderby'    => 'title',
				'fields'     => 'ids',
			)
		);

		$current_category_pos = array_search( $this->category_id, ( $categories ) );

		if ( false === $current_category_pos ) {
			return;
		}
		if ( $current_category_pos > 0 ) {
			$this->previous_category_id = $categories[ $current_category_pos - 1 ];
		}
		if ( $current_category_pos + 1 < count( $categories ) ) {
			$this->next_category_id = $categories[ $current_category_pos + 1 ];
		}
	}
}
