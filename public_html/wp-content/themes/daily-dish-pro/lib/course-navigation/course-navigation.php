<?php
/**
 * Adds navigation to courses.
 */

/**
 * Register custom fields for manual overwrite.
 */
require_once __DIR__ . '/custom-fields.php';

/**
 * Return true, if the course navigation is hidden.
 */
function crv_is_course_navigation_hidden() {
	if ( is_admin() || ! is_main_query() ) {
		return true;
	}

	// Bail, if it is not post nor category.
	if ( ! is_single() && ! is_category() ) {
		return true;
	}

	// Bail, if post is not in course.
	if ( is_single() ) {
		$not_in_course = 0 === count(
			get_posts(
				array(
					'include'  => get_the_ID(),
					'category' => 5792, // "DWZRLG" course.
					'fields'   => 'ids',
				)
			)
		);
		if ( $not_in_course ) {
			return true;
		}
	}

	// Bail, if category is not in course.
	if ( is_category() ) {
		$current_category_id = get_query_var( 'cat' );
		if ( ! cat_is_ancestor_of( 5792, $current_category_id ) ) {
			return true;
		}
	}

	// Every test passed, show navigation.
	return false;
}


/**
 * Add post navigation for posts in courses.
 * Wait for `is_single` conditional to load.
 */
add_action(
	'wp',
	function() {

		add_action(
			is_single() ? 'genesis_after_entry_content' : 'genesis_after_content',
			function() {
				if ( crv_is_course_navigation_hidden() ) {
					return;
				}

				if ( is_single() ) {
					require_once __DIR__ . '/class-crv-post-navigation.php';
					$navigation = new CrvPostNavigation();
				} else {
					require_once __DIR__ . '/class-crv-category-navigation.php';
					$navigation = new CrvCategoryNavigation( get_query_var( 'cat' ) );
				}

				$previous = $navigation->get_navigation_previous();
				$up       = $navigation->get_navigation_up();
				$next     = $navigation->get_navigation_next();

				$navigation = _navigation_markup( $previous . $up . $next, 'post-navigation', __( 'Post navigation' ), __( 'Posts' ) );
				echo wp_kses_post( $navigation );
			}
		);

	}
);

