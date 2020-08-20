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

// Register Custom Post Type for Linktree Cards.
function crv_register_linktree_card_cpt() {

	$args = array(
		'label'               => __( 'Linktree Card', 'crv_linktree' ),
		'description'         => __( 'A single card in the linktree', 'crv_linktree' ),
		'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 10,
		'menu_icon'           => 'dashicons-images-alt',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'rewrite'             => false,
		'capability_type'     => 'linktree_card',
		'map_meta_cap'        => true,
		'show_in_rest'        => false,
	);
	register_post_type( 'crv_linktree_card', $args );

}