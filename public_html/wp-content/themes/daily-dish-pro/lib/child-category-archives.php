<?php

/**
 * Setup category archives to list all direct child categories.
 *
 * - Only show posts assigned to a category and ignore posts from child-categories
 * - Add ACF Image field to categories
 * - Disallow adding posts to a parent category
 */


/**
 * Don't show child posts in category archives
 */
add_action(
	'parse_tax_query',
	function ( $query ) {
		if ( ! is_admin() && $query->is_main_query() && $query->is_category() ) {
			$query->tax_query->queries[0]['include_children'] = false;
		}
	}
);

/**
 * Add Category Featured Image with ACF
 */
add_action(
	'init',
	function () {

		if ( function_exists( 'acf_add_local_field_group' ) ) :

			acf_add_local_field_group(
				array(
					'key'                   => 'group_1',
					'title'                 => 'Vorschaubild',
					'fields'                => array(
						array(
							'key'               => 'field_1',
							'label'             => 'Vorschaubild',
							'name'              => 'featured_image',
							'type'              => 'image',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'return_format'     => 'id',
							'preview_size'      => 'thumbnail-portrait',
							'library'           => 'all',
							'min_width'         => '',
							'min_height'        => '',
							'min_size'          => '',
							'max_width'         => '',
							'max_height'        => '',
							'max_size'          => '',
							'mime_types'        => '',
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'taxonomy',
								'operator' => '==',
								'value'    => 'category',
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
);


/**
 * Disable selection of categories with child categories
 */
add_action(
	'added_term_relationship',
	function ( $object_id, $tt_id, $taxonomy ) {
		if ( 'category' != $taxonomy ) {
			return;
		}
		$term           = get_term_by( 'term_taxonomy_id', $tt_id, 'category' );
		$child_term_ids = get_categories(
			array(
				'parent' => $term->term_id,
				'fields' => 'ids',
			)
		);
		$is_childless   = count( $child_term_ids ) == 0;

		if ( $is_childless ) {
			return;
		}

		// Show an error message on the redirect
		add_filter(
			'redirect_post_location',
			function ( $location ) {
				return add_query_arg( 'parent_category_selected', 1, $location );
			}
		);
		// ... and remove the category from the post
		wp_remove_object_terms( $object_id, $term->term_id, 'category' );
	},
	10,
	3
);

/**
 * Show error message when parent category got selected
 */
add_action(
	'admin_notices',
	function () {
		if ( isset( $_GET['parent_category_selected'] ) ) {
			?>
	<div class="notice notice-warning is-dismissible">
	  <p>Du kannst dem Beitrag keine Kategorie zuordnen, die eine Unterkategorie enthält. Diese wurde automatisch wieder entfernt.</p>
	</div>
			<?php
		}
	}
);
add_filter(
	'removable_query_args',
	function ( $args ) {
		$args[] = 'parent_category_selected';
		return $args;
	}
);
