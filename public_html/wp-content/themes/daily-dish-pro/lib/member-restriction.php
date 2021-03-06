<?php

/**
 * Restrict content in member area to members and admins and
 * show excerpt and two images to non-members. Allow custom exerpts with ACF.
 */

/**
 * Restrict content for non-members and hide recipe metadata
 */
add_action(
	'wp',
	function () {
		if ( crv_user_is_unrestricted() ) {
			return;
		}

		// Restrict member content and metadata
		if ( is_single() && crv_is_restricted_post() ) {
			// Show excerpt and restriction message
			add_filter( 'the_content', 'crv_restricted_content', 100 );

			// Remove recipe metadata
			remove_filter( 'wpseo_schema_graph_pieces', array( 'WPRM_Metadata', 'wpseo_schema_graph_pieces' ), 1, 2 );

			// Don't insert any ads
			add_filter( 'ai_block_insertion_check', '__return_false' );
		}
	}
);

/**
 * Add `crv-restricted` body class for restricted posts
 */
add_filter(
	'body_class',
	function( $classes ) {
		if ( crv_is_restricted_post() ) {
			$classes[] = 'crv-restricted';
		}
		return $classes;
	}
);

/**
 * All posts in category 'member' are member-content
 */
function crv_is_restricted_post( $post_id = null ) {
	$post_id = $post_id ?? get_the_ID();
	if ( ! $post_id ) {
		return false;
	}
	return empty( crv_strip_restricted_posts( array( $post_id ) ) );
}

/**
 * Only returns post ids of not-restricted posts
 *
 * Not-restricted post: Not in category 'member' or in category 'free'
 */
function crv_strip_restricted_posts( $post_ids ) {
	return get_posts(
		array(
			'nopaging'  => true,
			'include'   => $post_ids,
			'fields'    => 'ids',
			'tax_query' => array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => 'member',
					'operator' => 'NOT IN',
				),
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => 'free',
				),
			),
		)
	);
}

/**
 * Show teaser image, excerpt, restriction message and post thumbnail.
 * The excerpt gets automatically generated, if not set in editor.
 */
function crv_restricted_content() {
	if ( get_field( 'custom_teaser' ) ) {
		$excerpt = wpautop( get_field( 'teaser_text' ) );
	} else {
		$content_without_shortcodes = preg_replace( '/<!--WPRM Recipe.*?<!--End WPRM Recipe-->/s', '', strip_shortcodes( get_the_content() ) );
		$excerpt                    = wpautop( wp_trim_words( $content_without_shortcodes ) );
	}

	$teaser_image   = wp_get_attachment_image( get_field( 'teaser_image' )['id'], 'full', false, array( 'class' => 'aligncenter' ) );
	$post_thumbnail = get_the_post_thumbnail( null, 'post-thumbnail', array( 'class' => 'aligncenter' ) );
	if ( rcp_user_has_active_membership() ) {
		$restrict_message = '<p class="restriciton-message">Du bist bereits Mitglied!<br>Nach der Veröffentlichung stehen dir alle Inhalte zur Verfügung.<br>Vielen Dank für dein Vertrauen!</p>';
	} else {
		global $rcp_options;

		$recipes_category_id   = 5869;
		$course_category_id    = 5792;
		$tutorials_category_id = 5287;

		$root_category = is_post_in_category( $recipes_category_id ) ? 'recipe'
			: ( is_post_in_category( $course_category_id ) ? 'course'
			: ( is_post_in_category( $tutorials_category_id ) ? 'tutorial' : 'recipe' ) );

		$restrict_message  = '<div class="restriciton-message">';
		$restrict_message .= '<p>Dieser Beitrag ist nur für Mitglieder verfügbar. Werde Mitglied, um Zugriff zu erhalten.</p>';
		$restrict_message .= '<a href="' . esc_url( add_query_arg( 'level', 2, get_permalink( $rcp_options['registration_page'] ) ) ) . '"><button class="cta-button cta-button--small">Jetzt Mitglied werden</button></a>';
		$restrict_message .= '<a href="' . esc_url( home_url() ) . '"><button class="cta-button cta-button--small">Mehr erfahren</button></a>';

		$restrict_message .= '<div class="restriction-links">';
		switch ( $root_category ) {
			case 'recipe':
				$primary_category_id   = crv_get_primary_taxonomy_id( get_the_ID() );
				$primary_category_name = get_category( $primary_category_id )->name;
				$primary_category_link = get_category_link( $primary_category_id );

				$restrict_message .= '<p><i>Noch nicht überzeugt?</i><br>Schau dir weitere Rohkost Rezepte aus unserem Mitgliederbereich an:</p>';
				$restrict_message .= '<a href="' . esc_url( $primary_category_link ) . '" target="_blank">' . esc_html( $primary_category_name ) . '</a>';
				break;
			case 'course':
				$restrict_message .= '<p><i>Noch nicht überzeugt?</i><br>Schau dir die Übersicht über den gesamten Kurs an:</p>';
				$restrict_message .= '<a href="' . esc_url( get_category_link( $course_category_id ) ) . '" target="_blank">' . esc_html( get_category( $course_category_id )->name ) . '</a>';
				break;
			case 'tutorial':
				$restrict_message .= '<p><i>Noch nicht überzeugt?</i><br>Schau dir die Übersicht über alle Tipps &amp; Tutorials an:<br>';
				$restrict_message .= '<a href="' . esc_url( get_category_link( $tutorials_category_id ) ) . '" target="_blank">' . esc_html( get_category( $tutorials_category_id )->name ) . '</a>';
				break;
		}
		$restrict_message .= '<p>Oder schau dir hier die Übersicht über alle Rohkost Rezepte an:</p>';
		$restrict_message .= '<a href="' . esc_url( get_category_link( 5869 ) ) . '" target="_blank">Alle Rohkost Rezepte</a>';
		$restrict_message .= '</div>';

		$restrict_message .= '<p><i>Bereits Mitglied?</i><br><a href="' . esc_url( get_permalink( get_page_by_path( 'login' ) ) ) . '">Hier geht\'s <b>zum&nbsp;Login</b></a>.</p>';
		$restrict_message .= '</div>';
	}

	return '<div class="make-90vw">'
		. $teaser_image
		. '</div>'
		. $excerpt
		. '<div class="restriction-wrapper">'
			. rcp_restricted_message_pending_verification( $restrict_message )
			. $post_thumbnail
		. '</div>';
}

/**
 * When saving a post (status: publish), automatically select the first image as teaser image, when no teaser image is selected already
 */
add_action(
	'acf/save_post',
	function ( $post_id ) {
		// Bail if already set or post is not published
		if ( get_field( 'teaser_image', $post_id ) || get_post_status( $post_id ) !== 'publish' ) {
			return;
		}

		$teaser_image_field = acf_get_local_field( 'teaser_image' )['key'];
		$first_image_id     = jw_get_first_image_id( get_post( $post_id )->post_content );
		if ( $teaser_image_field && $first_image_id ) {
			acf_save_post( $post_id, array( $teaser_image_field => $first_image_id ) );
		}
	}
);

/**
 * Register ACF for content restriction
 */
if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_5ea95be98a61e',
			'title'                 => 'Restricted Content Message',
			'fields'                => array(
				array(
					'key'               => 'field_5ea95e36a56f9',
					'label'             => 'Custom Teaser Text',
					'name'              => 'custom_teaser',
					'type'              => 'true_false',
					'instructions'      => 'Lege fest, ob du den Teaser Text selbst schreiben willst oder nicht. Ist diese Checkbox ausgewählt musst du deinen Text in das folgende Textfeld eingeben.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => 'Display Custom Teaser Text',
					'default_value'     => 0,
					'ui'                => 0,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
				),
				array(
					'key'               => 'field_5ea95dd0a56f8',
					'label'             => 'Teaser Text',
					'name'              => 'teaser_text',
					'type'              => 'textarea',
					'instructions'      => 'Dieser Text wird für beschränkte Beiträge anstelle des eigentlichen Inhalts angezeigt.',
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_5ea95e36a56f9',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'maxlength'         => '',
					'rows'              => '',
					'new_lines'         => '',
				),
				array(
					'key'               => 'field_5ea95ce0a56f7',
					'label'             => 'Teaser Bild',
					'name'              => 'teaser_image',
					'type'              => 'image',
					'instructions'      => 'Dieses Bild wird für beschränkte Beiträge zusätzlich angezeigt.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'array',
					'preview_size'      => 'full',
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

endif;
