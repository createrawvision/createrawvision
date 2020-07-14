<?php

require_once __DIR__ . '/../wp-cli-utils.php';

deploy_category_featured_images();
deploy_restrict_content_pro();
deploy_faqs();
deploy_nav_menus();
deploy_breadcrumbs();
deploy_teaser();
deploy_landing_page();
deploy_pages_for_templates();
deploy_featured_image();
deploy_private_posts();
deploy_bookmark_plugin();
deploy_support_plugin();


/*
 ###################
   #                 #
   #    FUNCTIONS    #
   #                 #
   ################### */

/**
 * Install Advanced Custom Fields
 * Remove posts from categories with child categories
 * Setting category image data from /deployment_data/category-images.json
 */
function deploy_category_featured_images() {
	WP_CLI::log( 'Installing and activating Advanced Custom Fields' );
	run_wp_cli_command( 'plugin install advanced-custom-fields --activate --force', array( 'exit_error' => true ) );

	WP_CLI::log( 'Removing categories from posts, when category has child categories' );

	$childless_category_ids = get_categories(
		array(
			'childless'  => true,
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);
	$all_category_ids       = get_categories(
		array(
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);
	$parent_category_ids    = array_values(
		array_diff(
			$all_category_ids,
			$childless_category_ids
		)
	);

	$post_ids_to_edit = get_posts(
		array(
			'category__in'   => $parent_category_ids,
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'post_status'    => 'any',
		)
	);

	foreach ( $post_ids_to_edit as $post_id ) {
		wp_remove_object_terms( $post_id, $parent_category_ids, 'category' );
	}
	WP_CLI::log( 'Removed parent categories from following posts: ' . ( implode( ',', $post_ids_to_edit ) ?: '(none)' ) );

	WP_CLI::log( 'Setting category featured image from JSON data' );
	$category_images_json = file_get_contents( ABSPATH . '../deployment_data/category-images.json' );
	$category_images      = json_decode( $category_images_json, $assoc = true );
	foreach ( $category_images as array(
		'term_name'   => $term_name,
		'term_id'     => $term_id,
		'image_title' => $image_title,
	) ) {

		// Get the image_id by title, skip if not found
		$images = get_posts(
			array(
				'post_type'   => 'attachment',
				'title'       => $image_title,
				'post_status' => null,
				'numberposts' => 1,
			)
		);
		if ( ! $images ) {
			WP_CLI::warning( "Couldn't find image with title ${image_title}. Skipping..." );
			continue;
		}
		$image_id = $images[0]->ID;

		// get term_id by id, then by name, skip if not found
		$term = get_term_by( 'id', $term_id, 'category' );
		if ( ! $term ) {
			WP_CLI::warning( "Couldn't find term with id ${term_id}. Skipping..." );
			$term = get_term_by( 'name', $term_name, 'category' );
		}
		if ( ! $term ) {
			WP_CLI::warning( "Couldn't find term with name ${term_name}. Trying id..." );
			continue;
		}
		$term_id = $term->term_id;

		$success = acf_save_post( 'term_' . $term_id, array( 'field_1' => $image_id ) );

		if ( ! $success ) {
			WP_CLI::warning( "Couldn't add image $image_title to term $term->name. Skipping..." );
		}
	}
}

/**
 * Install Restrict Content Pro
 * Make Settings and Membership Levels
 */
function deploy_restrict_content_pro() {
	WP_CLI::log( 'Installing and activating Restrict Content Pro' );
	$rcp_path = ABSPATH . '../deployment_data/restrict-content-pro.zip';
	run_wp_cli_command( "plugin install '$rcp_path' --activate --force", array( 'exit_error' => true ) );

	WP_CLI::log( 'Setting RCP Settings' );
	$rcp_settings         = array(
		'auto_renew'                           => '1',
		'currency'                             => 'EUR',
		'currency_position'                    => 'before',
		'gateways'                             => array( 'digistore' => '1' ),
		'email_template'                       => 'default',
		'from_name'                            => 'CreateRawVision',
		'from_email'                           => 'info@createrawvision.de',
		'admin_notice_emails'                  => 'info@createrawvision.de',
		'email_verification'                   => 'all',
		'verification_subject'                 => 'Bitte bestätige deine E-Mail Adresse',
		'verification_email'                   => 'Hallo %displayname%,

Bitte klicke hier, um deine E-Mail Adresse zu bestätigen:

%verificationlink%',
		'disable_active_email'                 => '1',
		'disable_free_email'                   => '1',
		'disable_trial_email'                  => '1',
		'disable_cancelled_email'              => '1',
		'disable_expired_email'                => '1',
		'disable_payment_received_email'       => '1',
		'disable_renewal_payment_failed_email' => '1',
		'disable_toolbar'                      => '1',
		'enable_terms'                         => '1',
		'terms_label'                          => 'AGBs zustimmen',
		'terms_link'                           => get_bloginfo( 'url' ) . '/agbs',
		'enable_privacy_policy'                => '1',
		'privacy_policy_label'                 => 'Datenschutzerklärung zustimmen',
		'privacy_policy_link'                  => get_privacy_policy_url(),
	);
	$current_rcp_settings = get_option( 'rcp_settings' );
	$new_rcp_settings     = wp_parse_args( $rcp_settings, $current_rcp_settings );
	update_option( 'rcp_settings', $new_rcp_settings );

	WP_CLI::warning( "RCP license key wasn't set. Add the license key manually." );

	WP_CLI::log( 'Creating membership levels' );
	$rcp_levels  = new RCP_Levels();
	$levels_args = array(
		array(
			'name'                => 'CreateRawVision Member (monatlich)',
			'description'         => 'Erhalte noch heute Zugriff zu über 400 großartigen Rezepte, hilfreichen Tipps &amp; Tricks und einer wertschätzenden Gemeinschaft.',
			'duration'            => '1',
			'duration_unit'       => 'month',
			'trial_duration'      => '0',
			'trial_duration_unit' => 'day',
			'price'               => '10',
			'fee'                 => '0',
			'maximum_renewals'    => '0',
			'after_final_payment' => '',
			'list_order'          => '0',
			'level'               => '0',
			'status'              => 'active',
			'role'                => 'subscriber',
		),
		array(
			'name'                => 'CreateRawVision Member (jährlich)',
			'description'         => 'Erhalte noch heute Zugriff zu über 400 großartigen Rezepte, hilfreichen Tipps &amp; Tricks und einer wertschätzenden Gemeinschaft.',
			'duration'            => '1',
			'duration_unit'       => 'year',
			'trial_duration'      => '0',
			'trial_duration_unit' => 'day',
			'price'               => '80',
			'fee'                 => '0',
			'maximum_renewals'    => '0',
			'after_final_payment' => '',
			'list_order'          => '0',
			'level'               => '0',
			'status'              => 'active',
			'role'                => 'subscriber',
		),
	);
	foreach ( $levels_args as $level_args ) {
		if ( $rcp_levels->get_level_by( 'name', $level_args['name'] ) ) {
			WP_CLI::warning( "Membership Level '${level_args['name']}' already exists. Not changing it." );
		} else {
			$rcp_levels->insert( $level_args );
		}
	}
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
		array( 'term_id' => $term_id ) = $term;

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

/**
 * Delete old menus
 * Create new main menu, main member menu and secondary menu
 * Assign items and locations
 */
function deploy_nav_menus() {
	WP_CLI::log( 'Creating new nav menus' );

	// Delete existing menus
	run_wp_cli_command( 'menu delete "Main Menu 2020"' );
	run_wp_cli_command( 'menu delete "Main Menu Member 2020"' );
	run_wp_cli_command( 'menu delete "Secondary Menu 2020"' );

	// Create new menus
	$main_menu_id        = run_wp_cli_command( 'menu create "Main Menu 2020" --porcelain', array( 'return' => 'stdout' ) );
	$main_menu_member_id = run_wp_cli_command( 'menu create "Main Menu Member 2020" --porcelain', array( 'return' => 'stdout' ) );
	$secondary_menu_id   = run_wp_cli_command( 'menu create "Secondary Menu 2020" --porcelain', array( 'return' => 'stdout' ) );

	WP_CLI::log( 'Creating main menus' );

	foreach ( array( $main_menu_id, $main_menu_member_id ) as $menu_id ) :

		$entry_menu_item_id = run_wp_cli_command( "menu item add-post $menu_id 19838 --porcelain", array( 'return' => 'stdout' ) ); // Neu hier?
		run_wp_cli_command( "menu item add-post $menu_id 19602 --parent-id=$entry_menu_item_id" ); // Über Uns
		run_wp_cli_command( "menu item add-post $menu_id 19655 --parent-id=$entry_menu_item_id" ); // Unsere Vision
		$faqs_id = get_page_by_path( 'faqs' )->ID;
		run_wp_cli_command( "menu item add-post $menu_id $faqs_id --parent-id=$entry_menu_item_id" ); // Häufig gestellte Fragen
		run_wp_cli_command( "menu item add-post $menu_id 19841 --title=Beste&nbsp;Beiträge --parent-id=$entry_menu_item_id" ); // Unsere Besten Beiträge

		if ( $menu_id === $main_menu_member_id ) {
			run_wp_cli_command( "menu item add-term $menu_id category 4269 --title=Mitgliederbereich" ); // Category 'member'
		}

		run_wp_cli_command( "menu item add-term $menu_id category 5869 --title=Rohkost&nbsp;Rezepte" );
		run_wp_cli_command( "menu item add-term $menu_id category 5287 --title=Rohkost&nbsp;Tipps" );

		/** @todo Link Blog main page? Create it? */
		$blog_menu_item_id = run_wp_cli_command( "menu item add-custom $menu_id 'Blog' '' --porcelain", array( 'return' => 'stdout' ) );
		run_wp_cli_command( "menu item add-term $menu_id category 5935 --parent-id=$blog_menu_item_id" ); // Bewusstsein & Achtsamkeit
		run_wp_cli_command( "menu item add-term $menu_id category 5937 --parent-id=$blog_menu_item_id" ); // Gesund Leben

		/** @todo add community (forum, q&a, events) or books item */

		if ( $menu_id === $main_menu_id ) {
			run_wp_cli_command( "menu item add-post $menu_id 1888 --title=E-Book" ); // "Dein Weg Zur Rohkost Leicht Gemacht" Buch
		}

		$advice_menu_item_id = run_wp_cli_command( "menu item add-custom $menu_id 'Empfehlungen' '' --porcelain", array( 'return' => 'stdout' ) );
		run_wp_cli_command( "menu item add-post $menu_id 18900 --title=Rohkost&nbsp;Ausstattung --parent-id=$advice_menu_item_id" );
		run_wp_cli_command( "menu item add-post $menu_id 19759 --title=Hochleistungsmixer --parent-id=$advice_menu_item_id" );
		run_wp_cli_command( "menu item add-post $menu_id 16743 --title=Dörrgeräte --parent-id=$advice_menu_item_id" );
		run_wp_cli_command( "menu item add-post $menu_id 16944 --title=Entsafter --parent-id=$advice_menu_item_id" );
		run_wp_cli_command( "menu item add-post $menu_id 16883 --title=Küchenmaschinen --parent-id=$advice_menu_item_id" );
		run_wp_cli_command( "menu item add-post $menu_id 16977 --title=Spiralschneider --parent-id=$advice_menu_item_id" );
		run_wp_cli_command( "menu item add-post $menu_id 16961 --title=Mandoline/Raspel/Hobel --parent-id=$advice_menu_item_id" );
		run_wp_cli_command( "menu item add-post $menu_id 19262 --title=Waffelschneider --parent-id=$advice_menu_item_id" );

  endforeach;

	// Create secondary menu items
	WP_CLI::log( 'Creating secondary menu' );

	run_wp_cli_command( "menu item add-custom $secondary_menu_id 'Kontakt & Coaching' '' --porcelain" );

	$work_with_me_menu_item_id = run_wp_cli_command( "menu item add-custom $secondary_menu_id 'Arbeite mit mir' '' --porcelain", array( 'return' => 'stdout' ) );
	run_wp_cli_command( "menu item add-custom $secondary_menu_id 'Workshops' '' --parent-id=$work_with_me_menu_item_id" );
	run_wp_cli_command( "menu item add-custom $secondary_menu_id 'Kooperationen' '' --parent-id=$work_with_me_menu_item_id" );
	run_wp_cli_command( "menu item add-custom $secondary_menu_id 'Rezeptentwicklung' '' --parent-id=$work_with_me_menu_item_id" );

	$facebook_svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M23.738.214v4.714h-2.804c-1.023 0-1.714.214-2.071.643s-.536 1.071-.536 1.929v3.375h5.232l-.696 5.286h-4.536v13.554h-5.464V16.161H8.309v-5.286h4.554V6.982c0-2.214.62-3.932 1.857-5.152S17.607 0 19.666 0c1.75 0 3.107.071 4.071.214z"/></svg>';
	$pinterest_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M4.571 10.661q0-1.929.67-3.634t1.848-2.973 2.714-2.196T13.107.465t3.607-.464q2.821 0 5.25 1.188t3.946 3.455 1.518 5.125q0 1.714-.339 3.357t-1.071 3.161-1.786 2.67-2.589 1.839-3.375.688q-1.214 0-2.411-.571t-1.714-1.571q-.179.696-.5 2.009t-.42 1.696-.366 1.268-.464 1.268-.571 1.116-.821 1.384-1.107 1.545l-.25.089-.161-.179q-.268-2.804-.268-3.357 0-1.643.384-3.688t1.188-5.134.929-3.625q-.571-1.161-.571-3.018 0-1.482.929-2.786t2.357-1.304q1.089 0 1.696.723t.607 1.83q0 1.179-.786 3.411t-.786 3.339q0 1.125.804 1.866t1.946.741q.982 0 1.821-.446t1.402-1.214 1-1.696.679-1.973.357-1.982.116-1.777q0-3.089-1.955-4.813t-5.098-1.723q-3.571 0-5.964 2.313t-2.393 5.866q0 .786.223 1.518t.482 1.161.482.813.223.545q0 .5-.268 1.304t-.661.804q-.036 0-.304-.054-.911-.268-1.616-1t-1.089-1.688-.58-1.929-.196-1.902z"/></svg>';
	$youtube_svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M29.7 10.3s-.3-2-1.1-2.8c-1.1-1.1-2.3-1.1-2.8-1.2C21.9 6 16 6 16 6s-5.9 0-9.8.3c-.6.1-1.7.1-2.8 1.2-.8.9-1.1 2.8-1.1 2.8S2 12.6 2 14.9v2.2c0 2.3.3 4.6.3 4.6s.3 2 1.1 2.8c1.1 1.1 2.5 1.1 3.1 1.2 2.2.2 9.5.3 9.5.3s5.9 0 9.8-.3c.5-.1 1.7-.1 2.8-1.2.8-.9 1.1-2.8 1.1-2.8s.3-2.3.3-4.6v-2.2c0-2.3-.3-4.6-.3-4.6zm-16.6 9.4v-8l7.6 4-7.6 4z"/></svg>';
	$twitter_svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M30.071 7.286q-1.196 1.75-2.893 2.982.018.25.018.75 0 2.321-.679 4.634t-2.063 4.437-3.295 3.759-4.607 2.607-5.768.973q-4.839 0-8.857-2.589.625.071 1.393.071 4.018 0 7.161-2.464-1.875-.036-3.357-1.152t-2.036-2.848q.589.089 1.089.089.768 0 1.518-.196-2-.411-3.313-1.991t-1.313-3.67v-.071q1.214.679 2.607.732-1.179-.786-1.875-2.054t-.696-2.75q0-1.571.786-2.911Q6.052 8.285 9.15 9.883t6.634 1.777q-.143-.679-.143-1.321 0-2.393 1.688-4.08t4.08-1.688q2.5 0 4.214 1.821 1.946-.375 3.661-1.393-.661 2.054-2.536 3.179 1.661-.179 3.321-.893z"/></svg>';
	$instagram_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M29.448 15.936c0 2.661-.029 4.502-.087 5.525-.116 2.416-.836 4.288-2.161 5.613s-3.195 2.045-5.613 2.161c-1.023.057-2.864.087-5.525.087s-4.502-.029-5.525-.087c-2.416-.116-4.287-.836-5.612-2.161s-2.045-3.195-2.161-5.613c-.059-1.021-.087-2.864-.087-5.525s.029-4.502.087-5.525c.116-2.416.836-4.287 2.161-5.612s3.195-2.045 5.612-2.161c1.021-.057 2.864-.087 5.525-.087s4.502.029 5.525.087c2.416.116 4.288.836 5.613 2.161s2.045 3.195 2.161 5.612c.059 1.023.087 2.864.087 5.525zM17.396 4.948c-.807.005-1.252.009-1.334.009s-.525-.004-1.334-.009c-.807-.005-1.42-.005-1.839 0-.418.005-.979.023-1.682.052s-1.302.088-1.795.175c-.495.088-.909.195-1.246.323-.58.232-1.093.57-1.534 1.011s-.779.954-1.011 1.534c-.129.338-.236.752-.323 1.246s-.145 1.093-.175 1.795c-.029.704-.046 1.264-.052 1.682s-.005 1.032 0 1.839c.005.807.009 1.252.009 1.334s-.004.525-.009 1.334c-.005.807-.005 1.42 0 1.839.005.418.023.979.052 1.682s.088 1.302.175 1.795c.088.495.195.909.323 1.246.232.58.57 1.093 1.011 1.534s.952.779 1.534 1.011c.338.129.752.236 1.246.323.493.087 1.093.145 1.795.175.704.029 1.264.046 1.682.052s1.03.005 1.839 0c.807-.005 1.252-.009 1.334-.009.08 0 .525.004 1.334.009.807.005 1.42.005 1.839 0 .418-.005.979-.023 1.682-.052s1.302-.087 1.795-.175c.493-.087.909-.195 1.246-.323.58-.232 1.093-.57 1.534-1.011s.779-.952 1.011-1.534c.129-.337.236-.752.323-1.246.087-.493.145-1.093.175-1.795.029-.704.046-1.264.052-1.682s.005-1.03 0-1.839c-.005-.807-.009-1.252-.009-1.334 0-.08.004-.525.009-1.334.005-.807.005-1.42 0-1.839-.005-.418-.023-.979-.052-1.682s-.087-1.302-.175-1.795c-.087-.493-.195-.909-.323-1.246-.232-.58-.57-1.093-1.011-1.534s-.954-.779-1.534-1.011c-.337-.129-.752-.236-1.246-.323S21.619 5.03 20.917 5c-.704-.029-1.264-.046-1.682-.052-.418-.007-1.03-.007-1.839 0zm3.531 6.125c1.336 1.336 2.004 2.957 2.004 4.862s-.668 3.527-2.004 4.863c-1.336 1.336-2.957 2.004-4.863 2.004s-3.527-.668-4.863-2.004c-1.338-1.336-2.005-2.957-2.005-4.863s.668-3.527 2.004-4.863c1.336-1.336 2.957-2.004 4.863-2.004 1.907 0 3.527.668 4.864 2.004zm-1.709 8.018c.871-.871 1.307-1.923 1.307-3.155s-.436-2.284-1.307-3.155-1.923-1.307-3.155-1.307-2.284.436-3.155 1.307-1.307 1.923-1.307 3.155.436 2.284 1.307 3.155 1.923 1.307 3.155 1.307 2.284-.436 3.155-1.307zm5.125-11.434c.314.314.471.691.471 1.132s-.157.82-.471 1.132c-.314.314-.691.471-1.132.471s-.82-.157-1.132-.471c-.314-.314-.471-.691-.471-1.132s.157-.82.471-1.132c.314-.314.691-.471 1.132-.471.441.002.818.159 1.132.471z"/></svg>';

	run_wp_cli_command( "menu item add-custom $secondary_menu_id '$facebook_svg' 'https://www.facebook.com/Create-Raw-Vision-596361277187093/'" );
	run_wp_cli_command( "menu item add-custom $secondary_menu_id '$pinterest_svg' 'https://de.pinterest.com/CreateRawVision'" );
	run_wp_cli_command( "menu item add-custom $secondary_menu_id '$youtube_svg' 'https://www.youtube.com/channel/UCDn-CVZvNd6xqXM0g1Zu4pg'" );
	run_wp_cli_command( "menu item add-custom $secondary_menu_id '$twitter_svg' 'https://twitter.com/CreateRawVision'" );
	run_wp_cli_command( "menu item add-custom $secondary_menu_id '$instagram_svg' 'https://www.instagram.com/createrawvision/'" );

	// Assign locations
	run_wp_cli_command( 'menu location remove hauptmenu primary' );
	run_wp_cli_command( "menu location assign $main_menu_id primary" );
	run_wp_cli_command( "menu location assign $secondary_menu_id secondary" );
}

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
 * Set the teaser data from ...
 * 1. /deployment_data/teaser-data.json
 * 2. If no image is set, select the first one
 */
function deploy_teaser() {
	WP_CLI::log( 'Setting teaser image for all member posts' );

	$teaser_json = file_get_contents( ABSPATH . '../deployment_data/teaser-data.json' );
	$teaser_data = $teaser_json ? json_decode( $teaser_json, $assoc = true ) : array();

	$field_keys = array(
		'custom_teaser' => acf_get_local_field( 'custom_teaser' )['key'],
		'teaser_text'   => acf_get_local_field( 'teaser_text' )['key'],
		'teaser_image'  => acf_get_local_field( 'teaser_image' )['key'],
	);

	$member_posts = get_posts(
		array(
			'numberposts'   => -1,
			'category_name' => 'member',
			'post_status'   => 'any',
		)
	);

	$progressbar = \WP_CLI\Utils\make_progress_bar( 'Creating teasers for all member posts', count( $member_posts ) );

	foreach ( $member_posts as $post ) {
		$data = array(
			$field_keys['custom_teaser'] => $teaser_data[ $post->ID ]['custom_teaser'] ?? null,
			$field_keys['teaser_text']   => $teaser_data[ $post->ID ]['teaser_text'] ?? null,
		);
		if ( isset( $teaser_data[ $post->ID ]['teaser_image_id'] ) ) {
			$data[ $field_keys['teaser_image'] ] = $teaser_data[ $post->ID ]['teaser_image_id'];
		} else {
			preg_match( '/<img.+?class=[\'"].*?wp-image-(\d*).*?[\'"].*?>/i', $post->post_content, $matches );
			if ( count( $matches ) == 0 ) {
				WP_CLI::warning( "Couldn't find first image in post $post->post_title" );
			} else {
				$first_image_id                      = $matches[1];
				$data[ $field_keys['teaser_image'] ] = $first_image_id;
			}
		}
		$success = acf_save_post( $post->ID, $data );

		if ( ! $success ) {
			WP_CLI::warning( "Couldn't add teaser data for post '$post->post_title' ($post->ID). Skipping..." );
		}

		$progressbar->tick();
	}
	$progressbar->finish();
}


/**
 * Creates the landing page for membership
 */
function deploy_landing_page() {
	WP_CLI::log( 'Creating landing page' );

	wp_insert_post(
		array(
			'post_content'  => '',
			'post_title'    => 'Member Landing Page',
			'post_name'     => 'member-landing',
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'page_template' => 'landing',
		)
	);
}


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


/**
 * Sets the featured images from `deployment_data/featured-images.json`
 */
function deploy_featured_image() {
	$featured_image_json = file_get_contents( ABSPATH . '../deployment_data/featured-images.json' );
	$featured_image_data = $featured_image_json ? json_decode( $featured_image_json, $assoc = true ) : array();

	$progressbar = \WP_CLI\Utils\make_progress_bar( 'Setting featured images from json data', count( $featured_image_data ) );

	foreach ( $featured_image_data as $post_id => $thumbnail_id ) {
		$success = set_post_thumbnail( $post_id, $thumbnail_id );

		if ( ! $success ) {
			WP_CLI::warning( "Couldn't set thumbnail for post $post_id" );
		}

		$progressbar->tick();
	}

	$progressbar->finish();
}


/**
 * Publishes all private posts from category member excluding the posts in `deployment_data/private-posts.json`
 */
function deploy_private_posts() {
	$excluded_posts_json = file_get_contents( ABSPATH . '../deployment_data/private-posts.json' );
	$excluded_posts      = $excluded_posts_json ? json_decode( $excluded_posts_json, $assoc = true ) : array();
	$excluded_post_ids   = array_map(
		function ( $post ) {
			return $post['id'];
		},
		$excluded_posts
	);

	$private_member_post_ids = get_posts(
		array(
			'numberposts' => -1,
			'category'    => get_category_by_slug( 'member' )->term_id,
			'post_status' => 'private',
			'fields'      => 'ids',
			'exclude'     => $excluded_post_ids,
		)
	);

	$progressbar = \WP_CLI\Utils\make_progress_bar( 'Publishing private posts', count( $private_member_post_ids ) );

	foreach ( $private_member_post_ids as $post_id ) {
		$success = 0 !== wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => 'publish',
			)
		);

		if ( ! $success ) {
			WP_CLI::warning( "Failed to update post {$post_id}" );
		}
		$progressbar->tick();
	}
	$progressbar->finish();
}


/**
 * Activates wp-bookmark plugin and sets options
 */
function deploy_bookmark_plugin() {
	WP_CLI::log( 'Activating and setting up wp-bookmarks' );

	// Actiavte plugin.
	run_wp_cli_command( 'plugin activate wp-bookmarks', array( 'exit_error' => true ) );

	// Set plugin options.
	$new_option = array(
		'wp_bookmark_popup_type'         => '1',
		'wpb_show_sharebutton'           => '0',
		'width'                          => '',
		'align'                          => 'left',
		'inline'                         => '0',
		'no_top_margin'                  => '0',
		'no_bottom_margin'               => '0',
		'pct_gap'                        => '5',
		'px_gap'                         => '20',
		'widgetized'                     => '1',
		'bookmark_hearticon'             => '0',
		'wpb_bookmark_category'          => '0',
		'wpb_add_collections'            => '1',
		'remove_bookmark'                => 'Lesezeichen entfernen',
		'dialog_bookmarked'              => 'Lesezeichen wurde hinzugefügt',
		'dialog_unbookmarked'            => 'Lesezeichen wurde entfernt',
		'default_collection'             => 'Standardkategorie',
		'add_to_collection'              => 'Zur Kategorie hinzufügen',
		'new_collection'                 => 'Neue Kategorie',
		'new_collection_placeholder'     => 'Name der Kategorie',
		'add_new_collection'             => 'Neue Kategorie erstellen',
		'bookmark_category'              => 'Lesezeichen für Kategorie erstellen',
		'remove_bookmark_category'       => 'Lesezeichen für Kategorie entfernen',
		'allow_multiple_bookmarks'       => '1',
		'auto_bookmark'                  => '0',
		'include_post_types'             =>
		array(
			0 => 'post',
			1 => 'page',
		),
		'exclude_ids'                    => '',
		'bookmarks_envato_purchase_code' => '',
		'wpb_show_users_avatar'          => '0',
		'wpb_new_collection_limit'       => '100',
		'wpb_bookmarks_limit'            => '100',
		'exclude_post_types'             => '',
	);

	$option = get_option( 'wpb', $new_option );
	$option = wp_parse_args( $new_option, $option );
	update_option( 'wpb', $option );

	WP_CLI::success( 'wp-bookmarks done' );
}


/**
 * Install and activate SupportCandy and tweak some settings
 */
function deploy_support_plugin() {
	WP_CLI::log( 'Installing and setting up supportcandy' );

	run_wp_cli_command( 'plugin install supportcandy --activate --force', array( 'exit_error' => true ) );

	WP_CLI::success( 'supportcandy done' );
}
