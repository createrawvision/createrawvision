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
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'easytimer', CHILD_URL . "/js/easytimer{$suffix}.js", array(), '4.3.0', true );
			wp_enqueue_style( 'daily-dish-front-style', CHILD_URL . '/style-front-page.css', array(), CHILD_THEME_VERSION );
			wp_enqueue_script( 'daily-dish-front-script', CHILD_URL . '/js/front-page.js', array( 'jquery', 'easytimer' ), CHILD_THEME_VERSION, true );
		}
	);

	// Avoid 'wpauto' for the front page.
	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'the_excerpt', 'wpautop' );

	// Remove entry header elements.
	remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
	remove_action( 'genesis_entry_header', 'genesis_do_post_title', 10 );
	remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 12 );

	/** @todo remove */
	// add_filter(
	// 'the_content',
	// function( $content ) {
	// if ( ! is_main_query() || ! in_the_loop() ) {
	// return $content;
	// }
	// return file_get_contents( ABSPATH . '/../deployment_data/homepage.html' );
	// }
	// );
}

// Run Genesis loop.
genesis();
