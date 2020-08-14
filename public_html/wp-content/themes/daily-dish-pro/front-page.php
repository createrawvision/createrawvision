<?php
/**
 * Daily Dish Pro.
 *
 * This file adds the front page to the Daily Dish Pro Theme.
 *
 * @package Daily Dish Pro
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    https://my.studiopress.com/themes/daily-dish/
 */

add_action( 'genesis_meta', 'daily_dish_home_genesis_meta' );
/**
 * Add widget support for homepage. If no widgets active, display the default loop.
 *
 * @since 1.0.0
 */
function daily_dish_home_genesis_meta() {

	if ( is_active_sidebar( 'home-top' ) || is_active_sidebar( 'home-middle' ) || is_active_sidebar( 'home-bottom' ) ) {

		// Force content-sidebar layout setting.
		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar' );

		// Add daily-dish-home body class.
		add_filter( 'body_class', 'daily_dish_body_class' );

		// Remove the default Genesis loop.
		remove_action( 'genesis_loop', 'genesis_do_loop' );

		// Add homepage widgets.
		add_action( 'genesis_loop', 'daily_dish_homepage_widgets' );

	}

}

/**
 * Define daily-dish-home body class.
 *
 * @param array $classes Current body classes.
 * @since 1.0.0
 *
 * @return array Modified body classes.
 */
function daily_dish_body_class( $classes ) {

	$classes[] = 'daily-dish-home';

	return $classes;

}

/**
 * Output front page widget areas.
 *
 * @since 1.0.0
 */
function daily_dish_homepage_widgets() {

	echo '<h2 class="screen-reader-text">' . __( 'Main Content', 'daily-dish-pro' ) . '</h2>';

	genesis_widget_area(
		'home-top',
		array(
			'before' => '<div class="home-top widget-area">',
			'after'  => '</div>',
		)
	);

	genesis_widget_area(
		'home-middle',
		array(
			'before' => '<div class="home-middle widget-area">',
			'after'  => '</div>',
		)
	);

	genesis_widget_area(
		'home-bottom',
		array(
			'before' => '<div class="home-bottom widget-area">',
			'after'  => '</div>',
		)
	);

}

/**
 * Enqueue scripts and styles for a static homepage
 */
if ( is_front_page() && ! is_home() ) {
	add_action(
		'wp_enqueue_scripts',
		function() {
			wp_enqueue_script( 'crv-countdown' );
			wp_enqueue_style( 'daily-dish-front-style', CHILD_URL . '/style-front-page.css', array(), CHILD_THEME_VERSION );
			wp_enqueue_script( 'daily-dish-front-script', CHILD_URL . '/js/front-page.js', array( 'jquery', 'easytimer' ), CHILD_THEME_VERSION, true );
		}
	);

	// Remove default loop.
	remove_action( 'genesis_loop', 'genesis_do_loop' );
	// Replace it with a template file.
	add_action(
		'genesis_loop',
		function() {
			load_template( CHILD_DIR . '/templates/front-page.php' );
		}
	);

}

// Run Genesis loop.
genesis();
