<?php
/**
 * Adds navigation to courses.
 *
 * Select which categories should count as course by setting the `crv_course_category_ids` option.
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

	$course_category_ids = get_option( 'crv_course_category_ids', array() );

	// Bail, if post is not child of course categories.
	if ( is_single() ) {
		$in_course = 0 < count(
			get_posts(
				array(
					'include'  => get_the_ID(),
					'category' => $course_category_ids,
					'fields'   => 'ids',
				)
			)
		);
		if ( ! $in_course ) {
			return true;
		}
	}

	// Bail, if category is not child of course categories.
	if ( is_category() ) {
		$current_category_id = get_query_var( 'cat' );

		$in_course = array_reduce(
			$course_category_ids,
			function( $in_course, $category_id ) use ( $current_category_id ) {
				return $in_course || cat_is_ancestor_of( $category_id, $current_category_id );
			},
			false
		);

		if ( ! $in_course ) {
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

