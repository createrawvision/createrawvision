<?php
/**
 * Show a shop with simple product links.
 */

add_action( 'init', 'crv_register_shop_item_cpt', 0 );
add_action( 'wp', 'crv_init_shop' );

/**
 * Show the template on the page 'shop'.
 */
function crv_init_shop() {
	// Bail, when on the wrong page.
	if ( ! is_page( 'shop' ) ) {
		return;
	}

	// Enqueue styles.
	add_action(
		'wp_enqueue_scripts',
		function () {
			wp_enqueue_style( 'crv-shop', CHILD_URL . '/css/shop.css', array(), CHILD_THEME_VERSION );
		}
	);

	// Show the template.
	add_action(
		'genesis_entry_content',
		function() {
			require CHILD_DIR . '/templates/shop.php';
		}
	);
}

/**
 * Register a custom post type with a taxonomy:
 * - Title
 * - Featured Image
 * - Page Attributes (for order)
 * - Custom fields
 *   - button text
 *   - URL
 */
function crv_register_shop_item_cpt() {

	// Register the post type.
	$args = array(
		'label'             => __( 'Shop Item', 'crv_shop' ),
		'supports'          => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ),
		'taxonomies'        => array( 'crv_shop_category' ),
		'public'            => false,
		'show_ui'           => true,
		'menu_icon'         => 'dashicons-cart',
		'show_in_admin_bar' => false,
		'rewrite'           => false,
		'capability_type'   => 'post',
	);
	register_post_type( 'crv_shop_item', $args );

	// Register the taxonomy.
	$args = array(
		'hierarchical'      => true,
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_tagcloud'     => false,
		'rewrite'           => false,
	);
	register_taxonomy( 'crv_shop_category', array( 'crv_shop_item' ), $args );

	// Register the custom fields.
	if ( function_exists( 'acf_add_local_field_group' ) ) :

		acf_add_local_field_group(
			array(
				'key'                   => 'group_5f3eb2a376a4c',
				'title'                 => 'Shop Item',
				'fields'                => array(
					array(
						'key'               => 'field_5f3eb2b40b645',
						'label'             => 'Button Text',
						'name'              => 'button_text',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
					),
					array(
						'key'               => 'field_5f3eb2de0b646',
						'label'             => 'Product URL',
						'name'              => 'product_url',
						'type'              => 'url',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'crv_shop_item',
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
