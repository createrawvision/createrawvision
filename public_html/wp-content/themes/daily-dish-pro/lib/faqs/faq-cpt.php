<?php

/**
 * Registers the `faq` post type.
 */
function faq_init()
{
	register_post_type('faq', array(
		'labels'                => array(
			'name'                  => __('FAQs', 'genesis'),
			'singular_name'         => __('FAQ', 'genesis'),
			'all_items'             => __('All FAQs', 'genesis'),
			'archives'              => __('FAQ Archives', 'genesis'),
			'attributes'            => __('FAQ Attributes', 'genesis'),
			'insert_into_item'      => __('Insert into FAQ', 'genesis'),
			'uploaded_to_this_item' => __('Uploaded to this FAQ', 'genesis'),
			'featured_image'        => _x('Featured Image', 'FAQ', 'genesis'),
			'set_featured_image'    => _x('Set featured image', 'FAQ', 'genesis'),
			'remove_featured_image' => _x('Remove featured image', 'FAQ', 'genesis'),
			'use_featured_image'    => _x('Use as featured image', 'FAQ', 'genesis'),
			'filter_items_list'     => __('Filter FAQs list', 'genesis'),
			'items_list_navigation' => __('FAQs list navigation', 'genesis'),
			'items_list'            => __('FAQs list', 'genesis'),
			'new_item'              => __('New FAQ', 'genesis'),
			'add_new'               => __('Add New', 'genesis'),
			'add_new_item'          => __('Add New FAQ', 'genesis'),
			'edit_item'             => __('Edit FAQ', 'genesis'),
			'view_item'             => __('View FAQ', 'genesis'),
			'view_items'            => __('View FAQs', 'genesis'),
			'search_items'          => __('Search FAQs', 'genesis'),
			'not_found'             => __('No FAQs found', 'genesis'),
			'not_found_in_trash'    => __('No FAQs found in trash', 'genesis'),
			'parent_item_colon'     => __('Parent FAQ:', 'genesis'),
			'menu_name'             => __('FAQs', 'genesis'),
		),
		'public'                => false,
		'show_ui'               => true,
		'rewrite'								=> false,
		'hierarchical'          => false,
		'supports'              => array('title', 'editor'),
		'menu_position'         => null,
		'menu_icon'             => 'dashicons-format-status',
		'show_in_rest'          => true,
		'rest_base'             => 'faq',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	));
}
add_action('init', 'faq_init');

/**
 * Sets the post updated messages for the `faq` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `faq` post type.
 */
function faq_updated_messages($messages)
{
	global $post;

	$permalink = get_permalink($post);

	$messages['faq'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf(__('FAQ updated. <a target="_blank" href="%s">View FAQ</a>', 'genesis'), esc_url($permalink)),
		2  => __('Custom field updated.', 'genesis'),
		3  => __('Custom field deleted.', 'genesis'),
		4  => __('FAQ updated.', 'genesis'),
		/* translators: %s: date and time of the revision */
		5  => isset($_GET['revision']) ? sprintf(__('FAQ restored to revision from %s', 'genesis'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
		/* translators: %s: post permalink */
		6  => sprintf(__('FAQ published. <a href="%s">View FAQ</a>', 'genesis'), esc_url($permalink)),
		7  => __('FAQ saved.', 'genesis'),
		/* translators: %s: post permalink */
		8  => sprintf(__('FAQ submitted. <a target="_blank" href="%s">Preview FAQ</a>', 'genesis'), esc_url(add_query_arg('preview', 'true', $permalink))),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf(
			__('FAQ scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview FAQ</a>', 'genesis'),
			date_i18n(__('M j, Y @ G:i', 'genesis'), strtotime($post->post_date)),
			esc_url($permalink)
		),
		/* translators: %s: post permalink */
		10 => sprintf(__('FAQ draft updated. <a target="_blank" href="%s">Preview FAQ</a>', 'genesis'), esc_url(add_query_arg('preview', 'true', $permalink))),
	);

	return $messages;
}
add_filter('post_updated_messages', 'faq_updated_messages');
