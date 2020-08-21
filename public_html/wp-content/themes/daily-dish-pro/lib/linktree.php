<?php

add_action( 'wp', 'crv_init_linktree' );
add_action( 'init', 'crv_register_linktree_card_cpt', 0 );

/**
 * Show the template on the page 'links'.
 */
function crv_init_linktree() {
	// Bail, when on the wrong page.
	if ( ! is_page( 'links' ) ) {
		return;
	}

	// Enqueue styles.
	add_action(
		'wp_enqueue_scripts',
		function () {
			wp_enqueue_style( 'crv-linktree', CHILD_URL . '/css/linktree.css', array(), CHILD_THEME_VERSION );
		}
	);

	// Show the template.
	add_action(
		'genesis_entry_content',
		function() {
			require CHILD_DIR . '/templates/linktree.php';
		}
	);
}

/**
 * Register Custom Post Type for Linktree Cards with URL field.
 */
function crv_register_linktree_card_cpt() {

	// Register post type.
	$args = array(
		'label'             => __( 'Linktree Card', 'crv_linktree' ),
		'description'       => __( 'A single card in the linktree', 'crv_linktree' ),
		'supports'          => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
		'hierarchical'      => false,
		'public'            => false,
		'show_ui'           => true,
		'menu_icon'         => 'dashicons-images-alt',
		'show_in_admin_bar' => false,
		'rewrite'           => false,
		'capability_type'   => 'linktree_card',
		'map_meta_cap'      => true,
	);
	register_post_type( 'crv_linktree_card', $args );

	// Register URL custom field.
	if ( function_exists( 'acf_add_local_field_group' ) ) :

		acf_add_local_field_group(
			array(
				'key'                   => 'group_5f3e96fbaca39',
				'title'                 => 'Linktree Link',
				'fields'                => array(
					array(
						'key'               => 'field_5f3e96fe74e47',
						'label'             => 'Linktree Link',
						'name'              => 'linktree_link',
						'type'              => 'link',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'return_format'     => 'url',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'crv_linktree_card',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);

		endif;
}
