<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_pages_for_templates();
};


/**
 * Creates empty pages for template files to use
 *
 * Pages:
 * * dashboard
 * * login
 */
function deploy_pages_for_templates() {
	WP_CLI::log( 'Creating dashboard page' );

	wp_insert_post(
		array(
			'post_content' => '',
			'post_title'   => 'Member Dashboard',
			'post_name'    => 'dashboard',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);

	WP_CLI::log( 'Creating login page' );

	wp_insert_post(
		array(
			'post_content' => '[login_form redirect="/dashboard"]',
			'post_title'   => 'Login',
			'post_name'    => 'login',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);

	WP_CLI::log( 'Creating search page' );

	wp_insert_post(
		array(
			'post_content' => '',
			'post_title'   => 'Erweiterte Rezept-Suche',
			'post_name'    => 'suche',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);
}

