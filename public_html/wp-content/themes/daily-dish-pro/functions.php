<?php

/**
 * Daily Dish Pro.
 *
 * This file adds the functions to the Daily Dish Pro Theme.
 *
 * @package Daily Dish Pro
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    https://my.studiopress.com/themes/daily-dish/
 */

// Start the engine.
require_once get_template_directory() . '/lib/init.php';

// Setup Theme.
require_once CHILD_DIR . '/lib/theme-defaults.php';

add_action( 'after_setup_theme', 'daily_dish_localization_setup' );
/**
 * Set Localization (do not remove).
 *
 * @since 1.0.0
 */
function daily_dish_localization_setup() {
	load_child_theme_textdomain( 'daily-dish-pro', CHILD_DIR . '/languages' );
}

// Include helper functions for the Daily Dish Pro theme.
require_once CHILD_DIR . '/lib/helper-functions.php';

// Add Color select to WordPress Theme Customizer.
require_once CHILD_DIR . '/lib/customize.php';

// Include Customizer CSS.
// include_once CHILD_DIR . '/lib/output.php';

// Include WooCommerce support.
require_once CHILD_DIR . '/lib/woocommerce/woocommerce-setup.php';

// Include the WooCommerce styles and related Customizer CSS.
require_once CHILD_DIR . '/lib/woocommerce/woocommerce-output.php';

// Include the Genesis Connect WooCommerce notice.
require_once CHILD_DIR . '/lib/woocommerce/woocommerce-notice.php';

// Child theme (do not remove).
define( 'CHILD_THEME_NAME', __( 'Daily Dish Pro', 'daily-dish-pro' ) );
define( 'CHILD_THEME_URL', 'https://my.studiopress.com/themes/daily-dish/' );
// define( 'CHILD_THEME_VERSION', '2.0.0' );
define( 'CHILD_THEME_VERSION', '0.1.32' );

add_action( 'wp_enqueue_scripts', 'daily_dish_enqueue_scripts_styles' );
/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 */
function daily_dish_enqueue_scripts_styles() {
	wp_enqueue_style( 'daily-dish-google-fonts', '//fonts.googleapis.com/css?family=Montserrat:400,600|Raleway:400,600&display=swap', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'daily-dish-ionicons', '/wp-content/themes/daily-dish-pro/fonts/ionicons.css', array(), CHILD_THEME_VERSION );
	if ( is_page( 'dein-weg-zur-rohkost-leicht-gemacht-3' ) ) {
		wp_enqueue_style( 'daily-dish-landing-style', '/wp-content/themes/daily-dish-pro/style-landing.css', array(), CHILD_THEME_VERSION );
	}

	wp_enqueue_script( 'daily-dish-global-script', CHILD_URL . '/js/global.js', array( 'jquery' ), CHILD_THEME_VERSION, true );

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'daily-dish-responsive-menu', CHILD_URL . "/js/responsive-menus{$suffix}.js", array( 'jquery' ), CHILD_THEME_VERSION, true );
	wp_localize_script(
		'daily-dish-responsive-menu',
		'genesis_responsive_menu',
		daily_dish_get_responsive_menu_settings()
	);

	// Register countdown styles for later use.
	wp_register_style( 'crv-countdown', CHILD_URL . '/css/countdown.css', array(), CHILD_THEME_VERSION );
}

/**
 * Responsive menu settings.
 *
 * @since 1.0.0
 *
 * @return array The menu settings.
 */
function daily_dish_get_responsive_menu_settings() {
	$settings = array(
		'mainMenu'         => __( 'Menu', 'daily-dish-pro' ),
		'menuIconClass'    => 'ionicon-before ion-android-menu',
		'subMenu'          => __( 'Submenu', 'daily-dish-pro' ),
		'subMenuIconClass' => 'ionicon-before ion-android-arrow-dropdown',
		'menuClasses'      => array(
			'combine' => array(
				'.nav-primary',
				'.nav-secondary',
			),
		),
	);

	return $settings;
}

// Add HTML5 markup structure.
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

// Add Accessibility support.
add_theme_support( 'genesis-accessibility', array( '404-page', 'drop-down-menu', 'headings', 'rems', 'search-form', 'skip-links' ) );

// Add viewport meta tag for mobile browsers.
add_theme_support( 'genesis-responsive-viewport' );

// Add support for custom background.
add_theme_support(
	'custom-background',
	array(
		'default-attachment' => 'fixed',
		'default-color'      => 'f5f5f5',
		'default-image'      => CHILD_URL . '/images/bg.png',
		'default-repeat'     => 'repeat',
		'default-position-x' => 'left',
	)
);

// Set up image sizes.
add_action(
	'after_setup_theme',
	function() {
		add_image_size( 'daily-dish-archive', 500, 262, true );
		add_image_size( 'thumbnail-portrait', 400, 600, true );
		add_image_size( 'small', 300, 9999 );
		add_image_size( 'tiny', 180, 9999 );

		// Add old image sizes, since some are referenced by URL in post_content.
		add_image_size( 'old-large', 1024, 1024 );
		add_image_size( 'old-medium', 300, 300 );
	}
);

add_filter(
	'image_size_names_choose',
	function( $sizes ) {
		$sizes['small'] = 'Klein';
		$sizes['tiny']  = 'Winzig';

		return $sizes;
	}
);

// Add support for custom header.
add_theme_support(
	'custom-header',
	array(
		'flex-height'     => true,
		'header_image'    => '',
		'header-selector' => '.site-title a',
		'header-text'     => false,
		'height'          => 200,
		'width'           => 745,
	)
);

// Unregister header right widget area.
unregister_sidebar( 'header-right' );

// Remove secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Remove site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

add_action( 'genesis_theme_settings_metaboxes', 'daily_dish_remove_genesis_metaboxes' );
/**
 * Remove navigation meta box.
 *
 * @since 1.0.0
 *
 * @param string $_genesis_theme_settings_pagehook The name of the page hook when the menu is registered.
 */
function daily_dish_remove_genesis_metaboxes( $_genesis_theme_settings_pagehook ) {

	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );
}

// Rename Primary and Secondary Menu.
add_theme_support(
	'genesis-menus',
	array(
		'secondary' => __( 'Before Header Menu', 'daily-dish-pro' ),
		'primary'   => __( 'After Header Menu', 'daily-dish-pro' ),
	)
);

// Remove output of primary navigation right extras.
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

add_action( 'genesis_meta', 'daily_dish_add_search_icon' );
/**
 * Add the search icon to header if option is set in Customizer.
 *
 * @since 1.0.0
 */
function daily_dish_add_search_icon() {
	$show_icon = get_theme_mod( 'daily_dish_header_search', daily_dish_customizer_get_default_search_setting() );

	// Exit early if option set to false.
	if ( ! $show_icon ) {
		return;
	}

	add_action( 'genesis_after_header', 'daily_dish_do_header_search_form', 14 );
	add_filter( 'genesis_nav_items', 'daily_dish_add_search_menu_item', 10, 2 );
	add_filter( 'wp_nav_menu_items', 'daily_dish_add_search_menu_item', 10, 2 );
}

/**
 * Modify Header Menu items.
 *
 * @param string $items Menu items.
 * @param object $args Menu arguments.
 * @since 2.0.0
 *
 * @return string The modified menu.
 */
function daily_dish_add_search_menu_item( $items, $args ) {

	$search_toggle = sprintf( '<li class="menu-item">%s</li>', daily_dish_get_header_search_toggle() );

	if ( 'primary' === $args->theme_location ) {
		$items .= $search_toggle;
	}

	return $items;
}

add_action( 'genesis_after_header', 'daily_dish_menu_open', 5 );
/**
 * Open markup for menu wrap.
 *
 * @since 2.0.0
 */
function daily_dish_menu_open() {
	echo '<div class="menu-wrap">';
}

add_action( 'genesis_after_header', 'daily_dish_menu_close', 15 );
/**
 * Close markup for menu wrap.
 *
 * @since 2.0.0
 */
function daily_dish_menu_close() {
	echo '</div>';
}

// Reposition secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_before', 'genesis_do_subnav' );


add_action( 'genesis_before', 'daily_dish_before_header' );
/**
 * Hook before header widget area before site container.
 *
 * @since 1.0.0
 */
function daily_dish_before_header() {
	genesis_widget_area(
		'before-header',
		array(
			'before' => '<div class="before-header"><div class="wrap">',
			'after'  => '</div></div>',
		)
	);
}

add_filter( 'genesis_post_info', 'daily_dish_single_post_info_filter' );
/**
 * Customize entry meta in entry header.
 *
 * @param string $post_info The entry meta.
 * @since 1.0.0
 *
 * @return string Modified entry meta.
 */
function daily_dish_single_post_info_filter( $post_info ) {

	$post_info = '[post_author_posts_link] &middot; [post_date] &middot; [post_comments] [post_edit]';

	return $post_info;
}

add_filter( 'genesis_author_box_gravatar_size', 'daily_dish_author_box_gravatar' );
/**
 * Modify size of the Gravatar in author box.
 *
 * @param int $size Current size.
 * @since 1.0.0
 *
 * @return int Modified size.
 */
function daily_dish_author_box_gravatar( $size ) {

	return 85;
}

add_filter( 'genesis_comment_list_args', 'daily_dish_comments_gravatar' );
/**
 * Modify size of the Gravatar in entry comments.
 *
 * @param array $args The avatar arguments.
 *
 * @return mixed Modified avatar arguments.
 */
function daily_dish_comments_gravatar( $args ) {

	$args['avatar_size'] = 48;

	return $args;
}

add_action( 'genesis_before_footer', 'daily_dish_before_footer_widgets', 5 );
/**
 * Hook before footer widget area above footer.
 *
 * @since 1.0.0
 */
function daily_dish_before_footer_widgets() {
	genesis_widget_area(
		'before-footer-widgets',
		array(
			'before' => '<div class="before-footer-widgets"><div class="wrap">',
			'after'  => '</div></div>',
		)
	);
}

add_action( 'genesis_after', 'daily_dish_after_footer' );
/**
 * Hook after footer widget after site container.
 *
 * @since 1.0.0
 */
function daily_dish_after_footer() {
	genesis_widget_area(
		'after-footer',
		array(
			'before' => '<div class="after-footer"><div class="wrap">',
			'after'  => '</div></div>',
		)
	);
}

// Add support for 3-column footer widgets.
add_theme_support( 'genesis-footer-widgets', 3 );

// Add support for after entry widget.
add_theme_support( 'genesis-after-entry-widget-area' );

// Relocate after entry widget.
remove_action( 'genesis_after_entry', 'genesis_after_entry_widget_area' );
add_action( 'genesis_after_entry', 'genesis_after_entry_widget_area', 5 );

// Register widget areas.
genesis_register_sidebar(
	array(
		'id'          => 'before-header',
		'name'        => __( 'Before Header', 'daily-dish-pro' ),
		'description' => __( 'Widgets in this section will display before the header on every page.', 'daily-dish-pro' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'home-top',
		'name'        => __( 'Home - Top', 'daily-dish-pro' ),
		'description' => __( 'Widgets in this section will display at the top of the homepage.', 'daily-dish-pro' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'home-middle',
		'name'        => __( 'Home - Middle', 'daily-dish-pro' ),
		'description' => __( 'Widgets in this section will display in the middle of the homepage.', 'daily-dish-pro' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'home-bottom',
		'name'        => __( 'Home - Bottom', 'daily-dish-pro' ),
		'description' => __( 'Widgets in this section will display at the bottom of the homepage.', 'daily-dish-pro' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'before-footer-widgets',
		'name'        => __( 'Before Footer', 'daily-dish-pro' ),
		'description' => __( 'Widgets in this section will display before the footer widgets on every page.', 'daily-dish-pro' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'after-footer',
		'name'        => __( 'After Footer', 'daily-dish-pro' ),
		'description' => __( 'Widgets in this section will display at the bottom of every page.', 'daily-dish-pro' ),
	)
);


/**
 * Set content width (so jetpack doesn't set sizes attribute to 1000px)
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1280;
}

// remove the footer credits
remove_action( 'genesis_footer', 'genesis_do_footer' );


/**
 * Jetpack "related posts" thumbnail size.
 */
function crv_relatedposts_thumbnail_size( $thumbnail_size ) {
	$thumbnail_size = array(
		'width'  => 400,
		'height' => 600,
	);
	return $thumbnail_size;
}
add_filter( 'jetpack_relatedposts_filter_thumbnail_size', 'crv_relatedposts_thumbnail_size' );

/**
 * Remove post meta on posts and preview
 */
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_after_post_content', 'genesis_post_meta' );

/**
 * Remove Jetpack Related Posts to add them in widget
 */
function jetpackme_remove_rp() {
	if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
		$jprp     = Jetpack_RelatedPosts::init();
		$callback = array( $jprp, 'filter_add_target_to_dom' );
		remove_filter( 'the_content', $callback, 40 );
	}
}
add_filter( 'wp', 'jetpackme_remove_rp', 20 );

/**
 * Hide Jetpack Related Post Heading
 */
add_filter(
	'jetpack_relatedposts_filter_headline',
	function () {
		return '';
	}
);


/*
 * Close newsletter popup and set cookie on form submission
 */
add_action( 'wp_footer', 'my_custom_popup_scripts', 500 );
function my_custom_popup_scripts() {
	echo "
	<script>
		(function ($, document, undefined) {
			jQuery('#popmake-7207 form').on('submit', function () {
				jQuery('#popmake-7207').trigger('pumSetCookie');
				setTimeout(function () {
					jQuery('#popmake-7207').popmake('close');
				}, 500);
			});
		}(jQuery, document))
    </script>
	";
}

/*
 * Disbale adding auto p elements for certain pages
 */
function remove_p_on_pages() {
	if ( is_page( 'dein-weg-zur-rohkost-leicht-gemacht-3' ) ) {
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_excerpt', 'wpautop' );
	}
}
add_action( 'wp_head', 'remove_p_on_pages' );

/*
 * Block the last words in titles form breaking into a new line by adding `&nbsp;`
 *
 * $length is the maximum number of non-breaking characters at the end
 */
function jw_prevent_akward_post_title_line_break( $title, $length = 17 ) {
	if ( ! is_single() || strlen( $title ) <= $length ) {
		return $title;
	}

	$lastNormalSpace = strpos( $title, ' ', -$length );
	if ( ! $lastNormalSpace ) {
		return $title;
	}
	$pos = min( strlen( $title ), $lastNormalSpace + 1 );
	return substr( $title, 0, $pos ) . str_replace( ' ', '&nbsp;', substr( $title, $pos ) );
}

add_filter( 'genesis_post_title_text', 'jw_prevent_akward_post_title_line_break' );


/**
 * On home, archives, search and custom loops, show posts in grid.
 */
add_action(
	'genesis_before_content',
	function() {
		if ( is_admin() || ! is_main_query() ) {
			return;
		}

		$show_grid = is_home() || is_archive() || is_search()
			|| ( is_singular( 'page' ) && genesis_get_custom_field( 'query_args' ) );

		if ( ! $show_grid ) {
			return;
		}

		// Remove text from preview.
		remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );

		// Add 'crv-grid' class to the 'content' tag.
		add_filter(
			'genesis_attr_content',
			function( $attributes ) {
				// Make grid large for category archives in "blog".
				$class = ' crv-grid';
				if ( is_archive() && cat_is_ancestor_of( get_category_by_slug( 'blog' ), get_queried_object() ) ) {
					$class .= ' crv-grid--large';
				}

				// Class is always set, at least to context.
				$attributes['class'] .= $class;
				return $attributes;
			}
		);

		// Display portraits for portrait thumbnails (overwriting default).
		add_action(
			'genesis_pre_get_option_image_size',
			function( $image_size ) {
				return jw_is_thumbnail_portrait() ? 'thumbnail-portrait' : $image_size;
			}
		);
	}
);


/**
 * Checks whether the current post thumbnail is higher than wide.
 */
function jw_is_thumbnail_portrait() {
	$post_thumbnail_id = get_post_thumbnail_id();
	$image             = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
	$image_width       = $image[1];
	$image_height      = $image[2];
	return $image_width < $image_height;
}


/**
 * Use the first image attatched to the member post for sharing when featured image is portrait.
 * When the set $image_url is not the thumbnail, return it instead.
 */
add_filter( 'wpseo_opengraph_image', 'jw_avoid_portrait_og_image' );

function jw_avoid_portrait_og_image( $image_url ) {
	// Bail, if $image_url was set manually.
	if ( wp_get_attachment_url( get_post_thumbnail_id() ) !== $image_url ) {
		return $image_url;
	}

	// Bail, if the thumbnail is not portrait.
	if ( ! jw_is_thumbnail_portrait() ) {
		return $image_url;
	}

	// Return the first image of a post, when it exists (assume it is not portrait).
	$first_image_url = jw_get_first_image_url( get_the_content() );
	if ( empty( $first_image_url ) ) {
		return $image_url;
	}
	return $first_image_url;
}

function jw_get_first_image_url( $post_content ) {
	$is_match = preg_match( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches );
	if ( ! $is_match ) {
		return null;
	}
	$first_image_url = $matches[1];
	return $first_image_url;
}

function jw_get_first_image_id( $post_content ) {
	preg_match( '/<img.+?class=[\'"].*?wp-image-(\d*).*?[\'"].*?>/i', $post_content, $matches );
	$first_image_id = $matches[1];
	return $first_image_id;
}

/**
 * Make all children of member category have body class `category-member`
 */
function crv_body_class_member_category( $classes ) {
	if ( ! is_admin() && is_main_query() && is_category() ) {
		$current_category_id = get_query_var( 'cat' );
		$member_category_id  = get_category_by_slug( 'member' )->term_id;

		if ( cat_is_ancestor_of( $member_category_id, $current_category_id ) ) {
			$classes[] = 'category-member';
		}
	}
	return $classes;
}
add_filter( 'body_class', 'crv_body_class_member_category' );

/**
 * Show all posts for member categories
 * (descendants of the 'member' category)
 * and order them by post title.
 * Add search when query param 's_cat' is set.
 */
function crv_modify_category_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_category ) {
		$current_category   = get_queried_object();
		$member_category_id = get_category_by_slug( 'member' )->term_id;

		if ( cat_is_ancestor_of( $member_category_id, $current_category ) ) {
			$query->set( 'posts_per_page', 9999 ); // Don't use `-1` for now, since relevanssi overwrites it wrong.
			$query->set( 'order', 'ASC' );
			$query->set( 'orderby', 'title' );
		}

		// Search posts for non-parent categories.
		$search_input = sanitize_text_field( wp_unslash( $_GET['s_cat'] ?? '' ) );
		if ( $current_category->count && $search_input ) {
			$query->set( 's', $search_input );
			$query->set( 'orderby', 'relevance' );
			$query->set( 'order', 'DESC' );

			// Activate Relevanssi without loading the search template, since `$query->is_search === false`.
			if ( function_exists( 'relevanssi_do_query' ) ) {
				add_filter(
					'the_posts',
					function( $posts, $query = false ) {
						$query = apply_filters( 'relevanssi_modify_wp_query', $query );
						return relevanssi_do_query( $query );
					},
					99,
					2
				);
			}
		}
	}
}
add_action( 'pre_get_posts', 'crv_modify_category_query' );

/**
 * Filter 'Roh & Vegan', 'Glutenfrei, Roh & Vegan' and similar stuff
 * from the post title on membership category archive pages
 */
function crv_filter_title_tail_for_member_archives( $title ) {
	if ( ! is_admin() && is_main_query() && is_category() ) {
		$current_cat_id = get_query_var( 'cat' );
		$member_cat_id  = get_category_by_slug( 'member' )->term_id;

		if ( $member_cat_id == $current_cat_id || cat_is_ancestor_of( $member_cat_id, $current_cat_id ) ) {
			return crv_filter_title_tail( $title );
		}
	}
	return $title;
}
/**
 * Cut the tail of the string, when it is only one of $match_words repeated at least 2 times
 * Non-word characters and HTML encoded entities are allowed between them
 *
 * **Examples**
 * ```
 * Rezept vegan                              --->  Rezept vegan
 * Mega-cooles Rezept - roh & vegan          --->  Mega-cooles Rezept
 * Mega-cooles Rezept &#45; roh &amp; vegan  --->  Mega-cooles Rezept
 * Mega-cooles roh-veganes Rezept            --->  Mega-cooles roh-veganes Rezept
 * "Rezept" - roh & vegan                    --->  "Rezept"
 * Rezept (cool) roh-vegan                   --->  Rezept (cool)
 * ```
 */
function crv_filter_title_tail( $title ) {
	$not_word    = '(\W|&[^;]*;)'; // HTML encoded entity or not word
	$match_words = implode( '|', array( 'probiotisch', 'roh', 'vegan', 'glutenfrei', 'selbstgemacht', 'und' ) );
	return preg_replace( "/\s(${not_word}*(${match_words})){2,}${not_word}*\$/i", '', $title );
}
add_filter( 'genesis_post_title_text', 'crv_filter_title_tail_for_member_archives' );

/**
 * List child categories for category archives
 */
require_once CHILD_DIR . '/lib/child-category-archives.php';

/**
 * FAQs
 */
// Register custom post type
require_once CHILD_DIR . '/lib/faqs/faq-cpt.php';

// Register custom taxonomy (categories)
require_once CHILD_DIR . '/lib/faqs/faq-category.php';

// Add custom query var to search FAQs
function jw_query_vars_faq_search( $vars ) {
	$vars[] = 'faq_search';
	return $vars;
}
add_filter( 'query_vars', 'jw_query_vars_faq_search' );

/**
 * Show different main menu for members
 */
add_filter(
	'wp_nav_menu_args',
	function ( $args ) {
		// If it's not the primary menu, bail
		if ( ! isset( $args['theme_location'] ) || $args['theme_location'] != 'primary' ) {
			return $args;
		}

		// Show different menus for RCP logged in users and admins.
		// Show it to members even before launch!
		if ( rcp_user_has_active_membership() || current_user_can( 'manage_options' ) ) {
			$args['menu'] = 'Main Menu Member 2020';
		} else {
			$args['menu'] = 'Main Menu 2020';
		}

		return $args;
	}
);


/**
 * Adds `nav-icon` class to nav items containing an svg element
 *
 * @todo add `sub-menu-toggle` to empty custom elements with children
 */
add_filter(
	'nav_menu_css_class',
	function ( $classes, $item, $args, $depth ) {
		if ( false === strpos( $item->title, '<svg' ) ) {
			return $classes;
		}

		$classes[] = 'nav-icon';
		return $classes;
	},
	10,
	4
);


/**
 * Disable breadcrumbs for search, since genesis doesn't distinguish between archive and search
 */
add_filter(
	'genesis_do_breadcrumbs',
	function ( $is_breadcrumb_hidden ) {
		return $is_breadcrumb_hidden || is_search();
	}
);

/**
 * Show all content to members or admins. But before launch block access for access level 0.
 */
function crv_user_is_unrestricted() {
	return current_user_can( 'manage_options' )
		|| rcp_user_has_access( 0, 1 )
		|| ( rcp_user_has_active_membership() && ! crv_is_before_membership_launch() );
}

/**
 * Restrict content in the member category to members
 */
require_once CHILD_DIR . '/lib/member-restriction.php';


/**
 * Show notice, when not on the production environment.
 */
add_action(
	'admin_notices',
	function () {
		if ( crv_is_production_env() ) {
			return;
		}
		?>
		<div class="notice notice-warning" style="background: linear-gradient(177deg ,hsla(60, 100%, 90%, 1) 40%, hsla(60, 100%, 70%, 1));">
			<p style="font-size: 1.5em;">Du befindest dich auf der Test-Website. Die meisten Änderungen werden <strong>nicht gespeichert</strong>.</p>
		</div>
		<?php
	}
);

/**
 * Return true, if the current environment is the production environment.
 */
function crv_is_production_env() {
	return 'https://createrawvision.de' === get_bloginfo( 'url' );
}


/**
 * Like `wp_loginout` returns link to login/logout depending on user being logged in.
 *
 * Redirects to dashboard on login. Stays on the same page on logout.
 */
function crv_loginout() {
	if ( ! is_user_logged_in() ) {
		return '<a href="/login">' . __( 'Log in' ) . '</a>';
	} else {
		return '<a href="' . esc_url( wp_logout_url( $_SERVER['REQUEST_URI'] ) ) . '">' . __( 'Log out' ) . '</a>';
	}
}

/**
 * Append Dashboard and Login In/Out link to menu with a redirect to this page
 */
function crv_loginout_menu_link( $menu, $args ) {
	if ( 'secondary' !== $args->theme_location ) {
		return $menu;
	}

	$menu .= is_user_logged_in() ? '<li class="menu-item"><a href="/dashboard">Übersichtsseite</a></li>' : '';
	$menu .= '<li class="menu-item">' . crv_loginout() . '</li>';
	return $menu;
}

add_filter( 'genesis_nav_items', 'crv_loginout_menu_link', 10, 2 );
add_filter( 'wp_nav_menu_items', 'crv_loginout_menu_link', 10, 2 );


/**
 * Adds an advanced recipe filter
 */
require_once CHILD_DIR . '/lib/recipe-filter.php';


/**
 * Connects Restrict Content Pro with MailChimp. Only on production!
 */
if ( crv_is_production_env() ) {
	require_once CHILD_DIR . '/lib/rcp-mailchimp.php';
}


/**
 * Add heart bookmark button to single post and page header
 */
function crv_show_bookmark_heart() {
	global $post, $wpb;

	$recipes     = class_exists( 'WPRM_Recipe_Manager' ) ? WPRM_Recipe_Manager::get_recipe_ids_from_post() : array();
	$object_name = $recipes ? 'Lieblingsrezept' : 'Lieblingsbeitrag';

	echo '<div class="wppopup__container">';
	echo '<a href="#TB_inline?width=300&height=300&inlineId=popup-view-' . $post->ID . '" class="wppopup thickbox wppopup-' . $post->ID;

	if ( $wpb->bookmarked( $post->ID ) ) {
		echo ' addedbookmark"><i class="fa fa-heart"></i><span class="wppopup__tooltip">' . esc_html( $object_name ) . ' entfernen</span>';
	} else {
		echo ' unbookmark"><i class="fa fa-heart-o"></i><span class="wppopup__tooltip">' . esc_html( $object_name ) . ' hinzufügen</span>';
	}

	echo '</a></div>';
}

/**
 * Enqueue the popup for bookmarking.
 */
function crv_enqueue_bookmark_popup() {
	global $post, $wpb;

	add_thickbox();

	echo '<div id="popup-view-' . $post->ID . '" class="bookmarpopup" style="display:none; text-align:center;">';
	echo $wpb->bookmarkpopup( $post->ID );
	echo '</div>';
}

/**
 * Sets up the bookmark popups and triggers.
 */
function crv_init_bookmarks() {
	global $post;

	// Bail in admin and outside singular content and for guests.
	if ( is_admin() || ! is_singular() || ! is_user_logged_in() ) {
		return;
	}

	// Hard excluded by post type.
	if ( wpb_get_option( 'include_post_types' ) ) {
		if ( is_array( wpb_get_option( 'include_post_types' ) ) && ! in_array( get_post_type(), wpb_get_option( 'include_post_types' ) ) ) {
			return;
		}
	}

	// Soft excluded by post id.
	if ( wpb_get_option( 'exclude_ids' ) ) {
		$array = explode( ',', wpb_get_option( 'exclude_ids' ) );
		if ( in_array( $post->ID, $array ) ) {
			return;
		}
	}

	add_action( 'wp_footer', 'crv_enqueue_bookmark_popup' );
	add_action( 'genesis_entry_header', 'crv_show_bookmark_heart', 14 );
	add_action( 'genesis_after_entry_content', 'crv_show_bookmark_heart', 11 );
}

add_action( 'wp', 'crv_init_bookmarks' );

/**
 * Support tickets for members get high priority by default
 */
add_filter(
	'wpsc_create_ticket_priority',
	function( $ticket_priority ) {
		if ( rcp_user_has_active_membership() ) {
			$high_priority_term = get_term_by( 'name', __( 'High', 'supportcandy' ), 'wpsc_priorities' );

			if ( $high_priority_term ) {
				return $high_priority_term->term_id;
			}
		}

		return $ticket_priority;
	}
);

/**
 * Assign Angie as default agent for tickets
 */
add_action(
	'wpsc_ticket_created',
	function( $ticket_id ) {
		global $wpscfunction;

		$user_id = get_user_by( 'slug', 'rawangela' )->ID;

		$agents = get_terms(
			array(
				'taxonomy'     => 'wpsc_agents',
				'hide_empty'   => false,
				'meta_key'     => 'user_id',
				'meta_value'   => $user_id,
				'meta_compare' => '=',
				'fields'       => 'ids',
			)
		);

		if ( ! $agents ) {
			return;
		}

		$wpscfunction->assign_agent( $ticket_id, array( $agents[0] ) );
	}
);

/**
 * Add a custom body class for customers (to hide some settings).
 */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( ! current_user_can( 'wpsc_agent' ) ) {
			$classes[] = 'wpsc_customer';
		}
		return $classes;
	}
);


/**
 * Show help button with popup on the bottom right.
 */
require_once CHILD_DIR . '/lib/help-popup.php';


global $crv_launch_date;
$crv_launch_date = new DateTime( '2020-08-20 12:00:00', new DateTimeZone( 'Europe/Berlin' ) );
/**
 * Returns true, when the date is before the membership launch
 */
function crv_is_before_membership_launch( $date = null ) {
	global $crv_launch_date;

	$date = $date ?? new DateTime();
	return $date < $crv_launch_date;
}


/**
 * Settings for RCP membership upgrades.
 */
require_once CHILD_DIR . '/lib/rcp-upgrade-settings.php';


/**
 * Set expiration to the launch day on days, which are at least one full day before launch.
 * Just for active memberships.
 * Reasion: DigiStore allows only full days of test phase.
 */
add_filter(
	'rcp_calculate_membership_level_expiration',
	function( $expiration_date, $membership_level, $set_trial ) {
		global $crv_launch_date;
		$day_before_launch = ( clone $crv_launch_date )->sub( new DateInterval( 'P1D' ) )->setTime( 0, 0, 0 );
		$now               = new DateTime( 'now' );

		if ( $now < $day_before_launch && 'active' === $membership_level->status ) {
			return $crv_launch_date->format( 'Y-m-d' ) . ' 23:59:59';
		}
		return $expiration_date;
	},
	10,
	3
);

/**
 * Display different total amount today on RCP register form until launch.
 */
add_filter(
	'rcp_registration_total',
	function( $total ) {
		global $crv_launch_date;
		$day_before_launch = ( clone $crv_launch_date )->sub( new DateInterval( 'P1D' ) )->setTime( 0, 0, 0 );
		$now               = new DateTime( 'now' );

		if ( $now < $day_before_launch ) {
			return 'Kostenlos bis zur Veröffentlichung';
		}
		return $total;
	}
);


/**
 * Display the header for pages with custom 'query_args'.
 *
 * @see genesis_do_loop()
 */
add_action(
	'genesis_before_loop',
	function() {
		// If genesis runs the custom loop, show the title of the page.
		if ( is_singular( 'page' ) && genesis_get_custom_field( 'query_args' ) ) {
			do_action( 'genesis_entry_header' );
		}
	}
);


/**
 * Add wrapper and header to rcp login form.
 */
add_action(
	'rcp_before_login_form_fields',
	function() {
		echo '<div class="rcp-login-form-container">';

		$site_icon_id = get_option( 'site_icon' );
		echo '<div class="rcp_login_form__header">';
		echo wp_get_attachment_image( $site_icon_id, $size = 'thumbnail', false, array( 'class' => 'rcp_login_form__icon' ) );
		echo '<h2 class="rcp_login_form__title">CreateRawVision</h2>';
		echo '<h3 class="rcp_login_form__subtitle">Mitgliederbereich</h3></div>';

		rcp_show_error_messages( 'login' );
	}
);

add_action(
	'rcp_after_login_form_fields',
	function() {
		echo '<a class="back-to-home" href="' . esc_url( home_url() ) . '">← Zurück zur Startseite</a>';
		echo '</div>';
	}
);


/**
 * Add a banner to every post (for non-members).
 * Hidden, when crv_hide_member_banner option is truthy.
 */
function crv_hide_banner() {
	return ! is_single()
		|| rcp_user_has_active_membership()
		|| crv_is_restricted_post( get_the_ID() )
		|| get_option( 'crv_hide_member_banner' );
}

add_action(
	'genesis_before_content',
	function() {
		if ( crv_hide_banner() ) {
			return;
		}

		include __DIR__ . '/templates/banner-membership.php';
	}
);

add_action(
	'wp_enqueue_scripts',
	function() {
		if ( crv_hide_banner() ) {
			return;
		}

		wp_enqueue_style( 'crv-countdown' );
	}
);

/**
 * Make the banner full width for 'unsere-vision'
 */
add_action(
	'genesis_attr_entry-header',
	function( $attributes ) {
		if ( ! is_page( array( 'unsere-vision', 'neu-hier' ) ) ) {
			return $attributes;
		}

		// Class is always set (at least context).
		$attributes['class'] .= ' full-width header-hero';
		return $attributes;
	}
);


/**
 * For category archives, prepend the unrestricted posts, when a query parameter was set.
 */
add_action(
	'pre_get_posts',
	function( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_category() ) {
			return;
		}

		// Only act, when query parameter was set.
		if ( isset( $_GET['free'] ) && $_GET['free'] ) {
			add_filter( 'the_posts', 'crv_unrestricted_posts_first', 100 ); // After relevanssi search.
		}
	}
);

/**
 * Keeps the original order, but prepends unrestricted posts.
 */
function crv_unrestricted_posts_first( $posts ) {
	// Find all unrestricted post at once and build a lookup table.
	$post_ids              = array_column( $posts, 'ID' );
	$unrestricted_post_ids = crv_strip_restricted_posts( $post_ids );
	$is_unrestricted_post  = array_combine( $unrestricted_post_ids, array_fill( 0, count( $unrestricted_post_ids ), true ) );

	// Group post being restricted / unrestricted.
	$restricted_posts   = array();
	$unrestricted_posts = array();

	// Preserve the original order in the posts array.
	foreach ( $posts as $post ) {
		if ( isset( $is_unrestricted_post[ $post->ID ] ) ) {
			$unrestricted_posts[] = $post;
		} else {
			$restricted_posts[] = $post;
		}
	}

	return array_merge( $unrestricted_posts, $restricted_posts );
}


/**
 * Show a linktree on the page '/links'.
 */
require_once CHILD_DIR . '/lib/linktree.php';


/**
 * Initialize a shop with just links to products.
 */
require_once CHILD_DIR . '/lib/shop.php';


/**
 * Don't show the edit post link, since it's in the admin bar.
 */
add_filter( 'genesis_edit_post_link', '__return_false' );


/**
 * Add comments count and post title to comments title.
 */
add_filter(
	'genesis_title_comments',
	function() {
		$comment_count     = get_comment_count( get_the_ID() )['approved'];
		$post_title_quoted = '"' . get_the_title() . '"';

		if ( 1 === $comment_count ) {
			$comments_title = 'Ein Kommentar zu ' . $post_title_quoted;
		} else {
			$comments_title = $comment_count . ' Kommentare zu ' . $post_title_quoted;
		}

		return sprintf( '<h2>%s</h2>', $comments_title );
	}
);

/**
 * Mark members (with active membership) in comments.
 */
add_filter(
	'comment_class',
	function( $classes, $class, $comment_id, $comment, $post_id ) {
		$user_id = $comment->user_id;
		if ( $user_id && rcp_user_has_active_membership( $user_id ) ) {
			$classes[] = 'bymember';
		}
		return $classes;
	},
	10,
	5
);

/**
 * Disable GDPR checkbox for unrestricted users.
 */
add_filter(
	'wpgdprc_wordpress_field',
	function( $field ) {
		if ( crv_user_is_unrestricted() ) {
			return '<input type="hidden" name="wpgdprc" value="1" />';
		}
		return $field;
	}
);

/**
 * Enable image upload only for unrestricted users.
 */
add_filter(
	'dco_ca_disable_attachment_field',
	function() {
		return ! crv_user_is_unrestricted();
	}
);

/**
 * Add post navigation for posts in courses.
 */
require_once CHILD_DIR . '/lib/course-navigation/course-navigation.php';

/**
 * Customize reorder-post-within-categories plugin.
 * Initial sort order by title for categories in member category.
 */
add_filter(
	'reorder_posts_within_category_initial_order',
	function ( $reverse, $post_type, $term_id ) {
		// Returns false, if term_id is not a valid category.
		return cat_is_ancestor_of( 4269, $term_id );
	},
	10,
	3
);
add_filter(
	'reorder_posts_within_category_initial_orderby',
	function ( $is_alphabetical, $post_type, $term_id ) {
		// Returns false, if term_id is not a valid category.
		return cat_is_ancestor_of( 4269, $term_id );
	},
	10,
	3
);

/**
 * Register shortcode to load parfait 3d cover template.
 */
add_shortcode(
	'crv-parfait-3d-cover',
	function() {
		ob_start();
		require CHILD_DIR . '/templates/parfait-3d-cover.php';
		return ob_get_clean();
	}
);

/**
 * Gets the primary term in the given taxonomy set via Yoast.
 * Falls back to first term, if there is none.
 *
 * @link https://wordpress.stackexchange.com/a/315577
 * @return int|false Taxonomy id or false, when there is none.
 */
function crv_get_primary_taxonomy_id( $post_id, $taxonomy = 'category' ) {
	$primary_term = false;
	if ( class_exists( 'WPSEO_Primary_Term' ) ) {
		$wpseo_primary_term = new WPSEO_Primary_Term( $taxonomy, $post_id );
		$primary_term       = $wpseo_primary_term->get_primary_term();
	}
	if ( ! is_object( $wpseo_primary_term ) || empty( $primary_term ) ) {
		$term = wp_get_post_terms( $post_id, $taxonomy );
		if ( isset( $term ) && ! empty( $term ) ) {
			return $term[0]->term_id;
		} else {
			return false;
		}
	}
	return $wpseo_primary_term->get_primary_term();
}

/**
 * Approve member comments automatically (when they are not spam).
 */
add_action(
	'pre_comment_approved',
	function( $approved ) {
		// $approved can be 0, 1, 'spam' or 'trash'. Alter only when 0.
		if ( crv_user_is_unrestricted() && 0 === $approved ) {
			$approved = 1;
		}

		return $approved;
	},
	99
);


/**
 * Checks if the post is in one of the categories or any child category.
 *
 * @param  int|string|array $category_ids (Single category id) or (comma separated string or array of category ids).
 * @param  int              $post_id      Post ID to check. Default to `get_the_ID()`.
 * @return bool `true`, iff post is in any category or child category.
 */
function is_post_in_category( $category_ids, $post_id = null ) {
	$args = array(
		'include'  => $post_id ?? get_the_ID(),
		'category' => $category_ids,
		'fields'   => 'ids',
	);
	return 0 < count( get_posts( $args ) );
}


/**
 * Add topfruits cooperation above register page
 */
add_action(
	'genesis_entry_header',
	function() {
		global $rcp_options;
		if ( ! is_page( $rcp_options['registration_page'] ) ) {
			return;
		}
		?>
		<div class="crv-cooperations">
			<span class="description">Eine Kooperation mit</span>
			<img src="<?php echo esc_url( CHILD_URL . '/images/topfruits-logo.jpg' ); ?>" alt="Topfruits">
		</div>
		<?php
	},
	4
);
