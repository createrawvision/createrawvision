<?php

require_once __DIR__ . '/../wp-cli-utils.php';

// Return the function to deploy all changes. Don't do anything.
return function () {
	deploy_breadcrumbs();
	deploy_faqs();
};


/**
 * Activate breadcrumbs for posts and archives
 */
function deploy_breadcrumbs() {
	WP_CLI::log( 'Activating breadcrumbs' );

	genesis_update_settings(
		array(
			'breadcrumb_single'     => 1,
			'breadcrumb_page'       => 0,
			'breadcrumb_404'        => 0,
			'breadcrumb_attachment' => 0,
			'breadcrumb_home'       => 0,
			'breadcrumb_front_page' => 0,
			'breadcrumb_posts_page' => 0,
			'breadcrumb_archive'    => 1,
		)
	);
}


/**
 * Create FAQs page
 * Create all FAQs from /deployment_data/faqs.json
 */
function deploy_faqs() {
	WP_CLI::log( 'Creating FAQs page, if not already existing' );
	$faqs_title = 'Häufig gestellte Fragen';
	$faqs_name  = 'faqs';
	if ( ! get_posts(
		array(
			'name'        => $faqs_name,
			'post_type'   => 'page',
			'post_status' => 'publish',
			'numberposts' => 1,
		)
	) ) {
		wp_insert_post(
			array(
				'post_title'  => $faqs_title,
				'post_name'   => $faqs_name,
				'post_type'   => 'page',
				'post_status' => 'publish',
			)
		);
	}

	// Create FAQs from JSON file
	$faqs_json     = file_get_contents( ABSPATH . '../deployment_data/faqs.json' );
	$category_objs = json_decode( $faqs_json );

	$category_progressbar = \WP_CLI\Utils\make_progress_bar( 'Creating all FAQ categories', count( $category_objs ) );

	foreach ( $category_objs as $category_obj ) {
		$category = $category_obj->category;

		// Create the category term
		$term = wp_insert_term(
			$category->name,
			'faq_category',
			array( 'slug' => $category->slug )
		);
		if ( is_wp_error( $term ) ) {
			WP_CLI::warning( $term->get_error_message() );
			continue;
		}
		list( 'term_id' => $term_id ) = $term;

		// Create all faqs
		$faqs = $category_obj->faqs;

		$items_progressbar = \WP_CLI\Utils\make_progress_bar( "Creating all FAQs in category {$category_obj->name}", count( $faqs ) );

		foreach ( $faqs as $faq ) {
			wp_insert_post(
				array(
					'post_title'   => $faq->title,
					'post_content' => $faq->content,
					'post_status'  => 'publish',
					'post_type'    => 'faq',
					'tax_input'    => array( 'faq_category' => array( $term_id ) ),
				)
			);

			$items_progressbar->tick();
		}

		$items_progressbar->finish();
		$category_progressbar->tick();
	}
	$category_progressbar->finish();

	// Create Category to collect uncategorized FAQs
	wp_insert_term(
		'Sonstige Fragen',
		'faq_category',
		array( 'slug' => 'other-faqs' )
	);
}
