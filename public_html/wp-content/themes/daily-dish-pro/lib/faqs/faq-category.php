<?php

/**
 * Registers the `faq_category` taxonomy,
 * for use with 'faq'.
 */
function faq_category_init()
{
	register_taxonomy('faq_category', array('faq'), array(
		'hierarchical'      => false,
		'public'            => false,
		'show_in_nav_menus' => false,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => false,
		'rewrite'           => false,
		'capabilities'      => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts',
		),
		'labels'            => array(
			'name'                       => __('FAQ categories', 'genesis'),
			'singular_name'              => _x('FAQ category', 'taxonomy general name', 'genesis'),
			'search_items'               => __('Search FAQ categories', 'genesis'),
			'popular_items'              => __('Popular FAQ categories', 'genesis'),
			'all_items'                  => __('All FAQ categories', 'genesis'),
			'parent_item'                => __('Parent FAQ category', 'genesis'),
			'parent_item_colon'          => __('Parent FAQ category:', 'genesis'),
			'edit_item'                  => __('Edit FAQ category', 'genesis'),
			'update_item'                => __('Update FAQ category', 'genesis'),
			'view_item'                  => __('View FAQ category', 'genesis'),
			'add_new_item'               => __('Add New FAQ category', 'genesis'),
			'new_item_name'              => __('New FAQ category', 'genesis'),
			'separate_items_with_commas' => __('Separate FAQ categories with commas', 'genesis'),
			'add_or_remove_items'        => __('Add or remove FAQ categories', 'genesis'),
			'choose_from_most_used'      => __('Choose from the most used FAQ categories', 'genesis'),
			'not_found'                  => __('No FAQ categories found.', 'genesis'),
			'no_terms'                   => __('No FAQ categories', 'genesis'),
			'menu_name'                  => __('FAQ categories', 'genesis'),
			'items_list_navigation'      => __('FAQ categories list navigation', 'genesis'),
			'items_list'                 => __('FAQ categories list', 'genesis'),
			'most_used'                  => _x('Most Used', 'faq_category', 'genesis'),
			'back_to_items'              => __('&larr; Back to FAQ categories', 'genesis'),
		),
		'show_in_rest'      => true,
		'rest_base'         => 'faq_category',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	));
}
add_action('init', 'faq_category_init');

/**
 * Sets the post updated messages for the `faq_category` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `faq_category` taxonomy.
 */
function faq_category_updated_messages($messages)
{

	$messages['faq_category'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __('FAQ category added.', 'genesis'),
		2 => __('FAQ category deleted.', 'genesis'),
		3 => __('FAQ category updated.', 'genesis'),
		4 => __('FAQ category not added.', 'genesis'),
		5 => __('FAQ category not updated.', 'genesis'),
		6 => __('FAQ categories deleted.', 'genesis'),
	);

	return $messages;
}
add_filter('term_updated_messages', 'faq_category_updated_messages');


/**
 * Set the default term when none is set
 */
function jw_set_default_faq_term($post_id, $post)
{
	$default_term = 'other';
	if ('faq' == $post->post_type && 'publish' === $post->post_status) {

		$terms = wp_get_post_terms($post_id, 'faq_category');
		if (empty($terms)) {
			wp_set_object_terms($post_id, $default_term, 'faq_category');
		}
	}
}
add_action('save_post', 'jw_set_default_faq_term', 100, 2);
