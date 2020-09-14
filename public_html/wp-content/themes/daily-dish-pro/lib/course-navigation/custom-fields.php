<?php
/**
 * Register the custom field for previous and next post/category navigation overwrite.
 */

add_action(
	'acf/init',
	function() {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_5f5f16e2c064c',
				'title'                 => 'Course Post Navigation Overwrite',
				'fields'                => array(
					array(
						'key'               => 'field_5f5f16f39f582',
						'label'             => 'Previous Post',
						'name'              => 'previous_post',
						'type'              => 'link',
						'instructions'      => 'Overwrite the previous post for navigation',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'return_format'     => 'array',
					),
					array(
						'key'               => 'field_5f5f2ac99f583',
						'label'             => 'Next Post',
						'name'              => 'next_post',
						'type'              => 'link',
						'instructions'      => 'Overwrite the next post for navigation',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'return_format'     => 'array',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'post',
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

		acf_add_local_field_group(
			array(
				'key'                   => 'group_5f5f5f34d443a',
				'title'                 => 'Course Category Navigation Overwrite',
				'fields'                => array(
					array(
						'key'               => 'field_5f5f5f6b0ff3e',
						'label'             => 'Previous Category',
						'name'              => 'previous_category',
						'type'              => 'taxonomy',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'taxonomy'          => 'category',
						'field_type'        => 'select',
						'allow_null'        => 1,
						'add_term'          => 0,
						'save_terms'        => 0,
						'load_terms'        => 0,
						'return_format'     => 'id',
						'multiple'          => 0,
					),
					array(
						'key'               => 'field_5f5f5f930ff3f',
						'label'             => 'Next Category',
						'name'              => 'next_category',
						'type'              => 'taxonomy',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'taxonomy'          => 'category',
						'field_type'        => 'select',
						'allow_null'        => 1,
						'add_term'          => 0,
						'save_terms'        => 0,
						'load_terms'        => 0,
						'return_format'     => 'id',
						'multiple'          => 0,
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
	}
);


