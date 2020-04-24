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
include_once get_template_directory() . '/lib/init.php';

// Setup Theme.
include_once get_stylesheet_directory() . '/lib/theme-defaults.php';

add_action('after_setup_theme', 'daily_dish_localization_setup');
/**
 * Set Localization (do not remove).
 *
 * @since 1.0.0
 */
function daily_dish_localization_setup()
{
	load_child_theme_textdomain('daily-dish-pro', get_stylesheet_directory() . '/languages');
}

// Include helper functions for the Daily Dish Pro theme.
require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Add Color select to WordPress Theme Customizer.
require_once get_stylesheet_directory() . '/lib/customize.php';

// Include Customizer CSS.
// include_once get_stylesheet_directory() . '/lib/output.php';

// Include WooCommerce support.
include_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Include the WooCommerce styles and related Customizer CSS.
include_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Include the Genesis Connect WooCommerce notice.
include_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

// Child theme (do not remove).
define('CHILD_THEME_NAME', __('Daily Dish Pro', 'daily-dish-pro'));
define('CHILD_THEME_URL', 'https://my.studiopress.com/themes/daily-dish/');
define('CHILD_THEME_VERSION', '2.0.0');

add_action('wp_enqueue_scripts', 'daily_dish_enqueue_scripts_styles');
/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 */
function daily_dish_enqueue_scripts_styles()
{

	wp_enqueue_style('daily-dish-google-fonts', '//fonts.googleapis.com/css?family=Montserrat:400,600|Raleway:400,600&display=swap', array(), CHILD_THEME_VERSION);
	wp_enqueue_style('daily-dish-ionicons', '/wp-content/themes/daily-dish-pro/fonts/ionicons.css', array(), CHILD_THEME_VERSION);
	if (is_page('dein-weg-zur-rohkost-leicht-gemacht-3')) {
		wp_enqueue_style('daily-dish-landing-style', '/wp-content/themes/daily-dish-pro/style-landing.css');
	}

	wp_enqueue_script('daily-dish-global-script', get_stylesheet_directory_uri() . '/js/global.js', array('jquery'), '1.0.0', true);

	$suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
	wp_enqueue_script('daily-dish-responsive-menu', get_stylesheet_directory_uri() . "/js/responsive-menus{$suffix}.js", array('jquery'), CHILD_THEME_VERSION, true);
	wp_localize_script(
		'daily-dish-responsive-menu',
		'genesis_responsive_menu',
		daily_dish_get_responsive_menu_settings()
	);
}

/**
 * Responsive menu settings.
 *
 * @since 1.0.0
 *
 * @return array The menu settings.
 */
function daily_dish_get_responsive_menu_settings()
{

	$settings = array(
		'mainMenu'         => __('Menu', 'daily-dish-pro'),
		'menuIconClass'    => 'ionicon-before ion-android-menu',
		'subMenu'          => __('Submenu', 'daily-dish-pro'),
		'subMenuIconClass' => 'ionicon-before ion-android-arrow-dropdown',
		'menuClasses'      => array(
			'combine'      => array(
				'.nav-secondary',
				'.nav-primary',
			),
		),
	);

	return $settings;
}

// Add HTML5 markup structure.
add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

// Add Accessibility support.
add_theme_support('genesis-accessibility', array('404-page', 'drop-down-menu', 'headings', 'rems', 'search-form', 'skip-links'));

// Add viewport meta tag for mobile browsers.
add_theme_support('genesis-responsive-viewport');

// Add support for custom background.
add_theme_support('custom-background', array(
	'default-attachment' => 'fixed',
	'default-color'      => 'f5f5f5',
	'default-image'      => get_stylesheet_directory_uri() . '/images/bg.png',
	'default-repeat'     => 'repeat',
	'default-position-x' => 'left',
));

// Add image sizes.
add_image_size('daily-dish-featured', 720, 470, true);
add_image_size('daily-dish-archive', 500, 262, true);
add_image_size('daily-dish-sidebar', 100, 100, true);

// Add support for custom header.
add_theme_support('custom-header', array(
	'flex-height'     => true,
	'header_image'    => '',
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'height'          => 200,
	'width'           => 745,
));

// Unregister header right widget area.
unregister_sidebar('header-right');

// Remove secondary sidebar.
unregister_sidebar('sidebar-alt');

// Remove site layouts.
genesis_unregister_layout('content-sidebar-sidebar');
genesis_unregister_layout('sidebar-content-sidebar');
genesis_unregister_layout('sidebar-sidebar-content');

add_action('genesis_theme_settings_metaboxes', 'daily_dish_remove_genesis_metaboxes');
/**
 * Remove navigation meta box.
 *
 * @since 1.0.0
 *
 * @param string $_genesis_theme_settings_pagehook The name of the page hook when the menu is registered.
 */
function daily_dish_remove_genesis_metaboxes($_genesis_theme_settings_pagehook)
{

	remove_meta_box('genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main');
}

// Rename Primary and Secondary Menu.
add_theme_support('genesis-menus', array(
	'secondary' => __('Before Header Menu', 'daily-dish-pro'),
	'primary'   => __('After Header Menu', 'daily-dish-pro'),
));

// Remove output of primary navigation right extras.
remove_filter('genesis_nav_items', 'genesis_nav_right', 10, 2);
remove_filter('wp_nav_menu_items', 'genesis_nav_right', 10, 2);

add_action('genesis_meta', 'daily_dish_add_search_icon');
/**
 * Add the search icon to header if option is set in Customizer.
 *
 * @since 1.0.0
 */
function daily_dish_add_search_icon()
{

	$show_icon = get_theme_mod('daily_dish_header_search', daily_dish_customizer_get_default_search_setting());

	// Exit early if option set to false.
	if (!$show_icon) {
		return;
	}

	add_action('genesis_after_header', 'daily_dish_do_header_search_form', 14);
	add_filter('genesis_nav_items', 'daily_dish_add_search_menu_item', 10, 2);
	add_filter('wp_nav_menu_items', 'daily_dish_add_search_menu_item', 10, 2);
}

/**
 * Modify Header Menu items.
 *
 * @param string $items Menu items.
 * @param array  $args Menu arguments.
 * @since 2.0.0
 *
 * @return string The modified menu.
 */
function daily_dish_add_search_menu_item($items, $args)
{

	$search_toggle = sprintf('<li class="menu-item">%s</li>', daily_dish_get_header_search_toggle());

	if ('primary' === $args->theme_location) {
		$items .= $search_toggle;
	}

	return $items;
}

add_action('genesis_after_header', 'daily_dish_menu_open', 5);
/**
 * Open markup for menu wrap.
 *
 * @since 2.0.0
 */
function daily_dish_menu_open()
{

	echo '<div class="menu-wrap">';
}

add_action('genesis_after_header', 'daily_dish_menu_close', 15);
/**
 * Close markup for menu wrap.
 *
 * @since 2.0.0
 */
function daily_dish_menu_close()
{

	echo '</div>';
}

// Reposition secondary navigation menu.
remove_action('genesis_after_header', 'genesis_do_subnav');
add_action('genesis_before', 'genesis_do_subnav');


add_action('genesis_before', 'daily_dish_before_header');
/**
 * Hook before header widget area before site container.
 *
 * @since 1.0.0
 */
function daily_dish_before_header()
{

	genesis_widget_area('before-header', array(
		'before' => '<div class="before-header"><div class="wrap">',
		'after'  => '</div></div>',
	));
}

add_filter('genesis_post_info', 'daily_dish_single_post_info_filter');
/**
 * Customize entry meta in entry header.
 *
 * @param string $post_info The entry meta.
 * @since 1.0.0
 *
 * @return string Modified entry meta.
 */
function daily_dish_single_post_info_filter($post_info)
{

	$post_info = '[post_author_posts_link] &middot; [post_date] &middot; [post_comments] [post_edit]';

	return $post_info;
}

add_filter('genesis_author_box_gravatar_size', 'daily_dish_author_box_gravatar');
/**
 * Modify size of the Gravatar in author box.
 *
 * @param int $size Current size.
 * @since 1.0.0
 *
 * @return int Modified size.
 */
function daily_dish_author_box_gravatar($size)
{

	return 85;
}

add_filter('genesis_comment_list_args', 'daily_dish_comments_gravatar');
/**
 * Modify size of the Gravatar in entry comments.
 *
 * @param array $args The avatar arguments.
 *
 * @return mixed Modified avatar arguments.
 */
function daily_dish_comments_gravatar($args)
{

	$args['avatar_size'] = 48;

	return $args;
}

add_action('genesis_before_footer', 'daily_dish_before_footer_widgets', 5);
/**
 * Hook before footer widget area above footer.
 *
 * @since 1.0.0
 */
function daily_dish_before_footer_widgets()
{

	genesis_widget_area('before-footer-widgets', array(
		'before' => '<div class="before-footer-widgets"><div class="wrap">',
		'after'  => '</div></div>',
	));
}

add_action('genesis_after', 'daily_dish_after_footer');
/**
 * Hook after footer widget after site container.
 *
 * @since 1.0.0
 */
function daily_dish_after_footer()
{

	genesis_widget_area('after-footer', array(
		'before' => '<div class="after-footer"><div class="wrap">',
		'after'  => '</div></div>',
	));
}

// Add support for 3-column footer widgets.
add_theme_support('genesis-footer-widgets', 3);

// Add support for after entry widget.
add_theme_support('genesis-after-entry-widget-area');

// Relocate after entry widget.
remove_action('genesis_after_entry', 'genesis_after_entry_widget_area');
add_action('genesis_after_entry', 'genesis_after_entry_widget_area', 5);

// Register widget areas.
genesis_register_sidebar(array(
	'id'          => 'before-header',
	'name'        => __('Before Header', 'daily-dish-pro'),
	'description' => __('Widgets in this section will display before the header on every page.', 'daily-dish-pro'),
));
genesis_register_sidebar(array(
	'id'          => 'home-top',
	'name'        => __('Home - Top', 'daily-dish-pro'),
	'description' => __('Widgets in this section will display at the top of the homepage.', 'daily-dish-pro'),
));
genesis_register_sidebar(array(
	'id'          => 'home-middle',
	'name'        => __('Home - Middle', 'daily-dish-pro'),
	'description' => __('Widgets in this section will display in the middle of the homepage.', 'daily-dish-pro'),
));
genesis_register_sidebar(array(
	'id'          => 'home-bottom',
	'name'        => __('Home - Bottom', 'daily-dish-pro'),
	'description' => __('Widgets in this section will display at the bottom of the homepage.', 'daily-dish-pro'),
));
genesis_register_sidebar(array(
	'id'          => 'before-footer-widgets',
	'name'        => __('Before Footer', 'daily-dish-pro'),
	'description' => __('Widgets in this section will display before the footer widgets on every page.', 'daily-dish-pro'),
));
genesis_register_sidebar(array(
	'id'          => 'after-footer',
	'name'        => __('After Footer', 'daily-dish-pro'),
	'description' => __('Widgets in this section will display at the bottom of every page.', 'daily-dish-pro'),
));


/**
 * Set content width (so jetpack doesn't set sizes attribute to 1000px)
 */
if (!isset($content_width)) {
	$content_width = 1280;
}

// remove the footer credits
remove_action('genesis_footer', 'genesis_do_footer');


/**
 * Jetpack "related posts" thumbnail size
 */
function crv_relatedposts_thumbnail_size($size)
{
	$size = array(
		'width'  => 350,
		'height' => 183
	);
	return $size;
}
add_filter('jetpack_relatedposts_filter_thumbnail_size', 'crv_relatedposts_thumbnail_size');

/**
 * Remove post meta on posts and preview
 */
remove_action('genesis_entry_footer', 'genesis_post_meta');
remove_action('genesis_after_post_content', 'genesis_post_meta');

/**
 * Smaller Comment Area 
 */
function modify_comment_form_text_area($arg)
{
	$arg['comment_field'] = '<p class="comment-form-comment">' .
		'<label for="comment">' . _x('Kommentar', 'noun') . '</label>' .
		'<textarea id="comment" name="comment" cols="45" rows="2" tabindex="4" aria-required="true"></textarea>' .
		'</p>';
	return $arg;
}
add_filter('comment_form_defaults', 'modify_comment_form_text_area');

/**
 * Remove Jetpack Related Posts to add them in widget
 */
function jetpackme_remove_rp()
{
	if (class_exists('Jetpack_RelatedPosts')) {
		$jprp = Jetpack_RelatedPosts::init();
		$callback = array($jprp, 'filter_add_target_to_dom');
		remove_filter('the_content', $callback, 40);
	}
}
add_filter('wp', 'jetpackme_remove_rp', 20);

/**
 * Hide Jetpack Related Post Heading
 */
add_filter('jetpack_relatedposts_filter_headline', function () {
	return '';
});


/*
 * Close newsletter popup and set cookie on form submission
 */
add_action('wp_footer', 'my_custom_popup_scripts', 500);
function my_custom_popup_scripts()
{
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
function remove_p_on_pages()
{
	if (is_page('dein-weg-zur-rohkost-leicht-gemacht-3')) {
		remove_filter('the_content', 'wpautop');
		remove_filter('the_excerpt', 'wpautop');
	}
}
add_action('wp_head', 'remove_p_on_pages');


/*
* DISABLED
* Add a banner for "Dein Weg Zur Rohkost Leicht Gemacht"
*/
function load_book_banner()
{
	wp_enqueue_style('book-banner-style', get_stylesheet_directory_uri() . '/lib/banner/style-banner.css');
	readfile(get_stylesheet_directory() . '/lib/banner/dein-weg-zur-rohkost-banner.min.html');
}
function show_book_banner_for_home()
{
	if (is_home()) {
		load_book_banner();
	}
}
function show_book_banner_for_posts()
{
	if (is_single()) {
		load_book_banner();
	}
}

// add_action('genesis_before_content_sidebar_wrap', 'show_book_banner_for_home');
// add_action('genesis_entry_content', 'show_book_banner_for_posts', 8);


/*
 * DISABLED
 * Add AdSense only to posts 
function display_adsense()
{
	if (is_single() && ! is_single('gesunder-kaffee-ersatz-dr-switzers-karob-kaffee-tonikum-roh-vegan')) {
		echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
	(adsbygoogle = window.adsbygoogle || []).push({
		google_ad_client: "ca-pub-8171451394460301",
		enable_page_level_ads: true
	});
</script>';
	}
}
add_action( 'wp_head', 'display_adsense' );
 */


/*
* Adds REST Endpoint /crv/subscribe for subscribing to Mailchimp with Digistore Coupon
*/
include_once get_stylesheet_directory() . '/lib/subscribe_with_coupon.php';

function add_subscribe_with_coupon()
{
	register_rest_route('crv', '/subscribe', array(
		'methods' => 'POST',
		'callback' => 'subscribe_with_coupon'
	));
}

add_action('rest_api_init', 'add_subscribe_with_coupon');


/*
 * Block the last words form breaking into new line by adding &nbsp;
 * $length is the maximum number of non-breaking characters at the end
 */
function jw_preventAkwardPostTitleLineBreak($title, $length = 17)
{
	if (!is_single() || strlen($title) <= $length)
		return $title;

	$lastNormalSpace = strpos($title, ' ', -$length);
	if (!$lastNormalSpace)
		return $title;
	$pos = min(strlen($title), $lastNormalSpace + 1);
	return substr($title, 0, $pos) . str_replace(' ', '&nbsp;', substr($title, $pos));
}

add_filter('genesis_post_title_text', 'jw_preventAkwardPostTitleLineBreak');


/*
* Make portrait thumbnails when original image was portrait
*/
add_image_size('thumbnail-portrait', 400, 600, true);
add_action('genesis_pre_get_option_image_size', 'jw_portait_thumbnail', 11);

function jw_portait_thumbnail($image_size)
{
	if (
		(is_home() || is_archive() || is_search())
		&& is_main_query()
		&& jw_is_thumbnail_portrait()
	) {
		return 'thumbnail-portrait';
	}
	return $image_size;
}

function jw_is_thumbnail_portrait()
{
	$post_thumbnail_id = get_post_thumbnail_id();
	$image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
	$image_width = $image[1];
	$image_height = $image[2];
	return $image_width < $image_height;
}

/*
* Don't show excerpts and entry meta on archives, home or search
*/
function jw_remove_excerpts_and_entry_meta()
{
	if ((is_home() || is_archive() || is_search()) && is_main_query()) {
		remove_action('genesis_entry_content', 'genesis_do_post_content');
		remove_action('genesis_entry_header', 'genesis_post_info', 12);
	}
}

add_action('genesis_before_content', 'jw_remove_excerpts_and_entry_meta', 1);

/**
 * Use the first image attatched to the member post for sharing when featured image is portrait
 */
add_filter('wpseo_opengraph_image', 'jw_avoid_portrait_og_image');

function jw_avoid_portrait_og_image($image_url)
{
	if (!jw_is_thumbnail_portrait()) {
		return $image_url;
	}
	$first_image_url = jw_get_first_image_url();
	if (empty($first_image_url)) {
		return $image_url;
	}
	return $first_image_url;
}

function jw_get_first_image_url()
{
	global $post;
	preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_image_url = $matches[1];
	return $first_image_url;
}


/**
 * Make all children of member category have body class `category-member`
 */
function crv_body_class_member_category($classes)
{
	if (!is_admin() && is_main_query() && is_category()) {
		$current_category_id = get_query_var('cat');
		$member_category_id = get_category_by_slug('member')->term_id;

		if (cat_is_ancestor_of($member_category_id, $current_category_id)) {
			$classes[] = 'category-member';
		}
	}
	return $classes;
}
add_filter('body_class', 'crv_body_class_member_category');

/**
 * Show more posts for member categories (which are descendants of the 'member' category)
 */
function crv_show_all_posts_for_member_categories($query)
{
	if (!is_admin() && $query->is_main_query() && $query->is_category) {
		$current_category = $query->query_vars['category_name'];
		$current_category_id = get_category_by_slug($current_category)->term_id;
		$member_category_id = get_category_by_slug('member')->term_id;

		if ('member' == $current_category) {
			$query->set('posts_per_page', 20);
		} elseif (cat_is_ancestor_of($member_category_id, $current_category_id)) {
			$query->set('posts_per_page', -1);
			$query->set('order', 'ASC');
			$query->set('orderby', 'title');
		}
	}
}
add_action('pre_get_posts', 'crv_show_all_posts_for_member_categories');


/**
 * Filter 'Roh & Vegan', 'Glutenfrei, Roh & Vegan' and similar stuff 
 * from the post title on membership category pages
 */
function crv_filter_title_tail_for_member_archives($title)
{
	if (!is_admin() && is_main_query() && is_category()) {
		$current_cat_id = get_query_var('cat');
		$member_cat_id = get_category_by_slug('member')->term_id;

		if ($member_cat_id == $current_cat_id || cat_is_ancestor_of($member_cat_id, $current_cat_id)) {
			return crv_filter_title_tail($title);
		}
	}
	return $title;
}
/**
 * Cut the tail of the string, when it is only one of $match_words repeated at least 2 times
 * Non-word characters and HTML encoded entities are allowed between them
 */
function crv_filter_title_tail($title)
{
	$not_word = '(\W|&[^;]*;)'; // HTML encoded entity or not word 
	$match_words = implode('|', ['probiotisch', 'roh', 'vegan', 'glutenfrei', 'selbstgemacht', 'und']);
	return preg_replace("/(${not_word}+(${match_words})){2,}${not_word}*\$/i", '', $title);
}
add_filter('genesis_post_title_text', 'crv_filter_title_tail_for_member_archives');


/**
 * Register custom payment gateways for restrict content pro
 */
function crv_rcp_register_custom_gateways($gateways)
{
	require_once get_stylesheet_directory() . '/lib/class-rcp-payment-gateway-digistore.php';

	$gateways['digistore'] = array(
		'label' => 'Digistore',
		'admin_label' => 'Digistore',
		'class' => 'RCP_Payment_Gateway_Digistore'
	);

	return $gateways;
}
add_filter('rcp_payment_gateways', 'crv_rcp_register_custom_gateways');


/**
 * Allow DigiStore subscriptions to be cancelled
 */
function crv_allow_digistore_cancellation($can_cancel, $membership_id, $membership)
{
	if (
		$membership->is_recurring()
		&& 'active' == $membership->get_status()
		&& $membership->is_paid()
		&& !$membership->is_expired()
		&& 'digistore' == $membership->get_gateway()
	) {
		return true;
	}

	return $can_cancel;
}
add_filter('rcp_membership_can_cancel', 'crv_allow_digistore_cancellation', 10, 3);

function crv_cancel_digistore_subscription($success, $gateway, $gateway_subscription_id)
{
	if ($gateway != 'digistore') {
		return $success;
	}

	$gateways = new RCP_Payment_Gateways;
	$gateway_digistore  = $gateways->get_gateway('digistore');
	$gateway_digistore  = new $gateway_digistore['class']();

	return $gateway_digistore->stop_rebilling($gateway_subscription_id);
}
add_filter('rcp_membership_payment_profile_cancelled', 'crv_cancel_digistore_subscription', 10, 3);


/**
 * Redirect invoices to digistore by checking payment meta to include invoice url
 */
function crv_trigger_digistore_invoice_download()
{
	if (!isset($_GET['rcp-action']) || 'download_invoice' != $_GET['rcp-action']) {
		return;
	}

	global $rcp_payments_db;

	$payment_id = absint($_GET['payment_id']);

	$digistore_invoice_url = $rcp_payments_db->get_meta($payment_id, 'digistore_invoice_url', true);

	if (empty($digistore_invoice_url)) {
		return;
	}

	wp_redirect($digistore_invoice_url);
	exit;
}
add_action('init', 'crv_trigger_digistore_invoice_download', 9);


/**
 * Add the DigiStore Product ID to the subscription level form
 */
function crv_digistore_product_id_form_field($product_id = '')
{
?>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="crv-digistore-product-id"><?php _e('DigiStore Product-ID'); ?></label>
		</th>
		<td>
			<input id="crv-digistore-product-id" type="text" name="digistore_product_id" value="<?php echo $product_id; ?>" />
		</td>
	</tr>
	<?php
}

function crv_digistore_product_id_form_field_edit($level)
{
	global $rcp_levels_db;
	$product_id = $rcp_levels_db->get_meta($level->id, 'digistore_product_id', true);

	crv_digistore_product_id_form_field($product_id);
}

add_action('rcp_add_subscription_form', 'crv_digistore_product_id_form_field');
add_action('rcp_edit_subscription_form', 'crv_digistore_product_id_form_field_edit');


/**
 * Add the DigiStore Product ID as subscription level meta
 */
function crv_add_digistore_product_id($level_id, $args)
{
	if (empty($args['digistore_product_id'])) {
		return;
	}

	global $rcp_levels_db;

	$rcp_levels_db->update_meta($level_id, 'digistore_product_id', trim($args['digistore_product_id']));
}
add_action('rcp_add_subscription', 'crv_add_digistore_product_id', 10, 2);
add_action('rcp_edit_subscription_level', 'crv_add_digistore_product_id', 10, 2);


/**
 * Don't show child posts in category archives
 */
add_action('parse_tax_query', function ($query) {
	if (!is_admin() && $query->is_main_query() && $query->is_category()) {
		$query->tax_query->queries[0]['include_children'] = false;
	}
});

/**
 * Add Category Featured Image with ACF
 */
add_action('init', function () {

	if (function_exists('acf_add_local_field_group')) :

		acf_add_local_field_group(array(
			'key' => 'group_1',
			'title' => 'Vorschaubild',
			'fields' => array(
				array(
					'key' => 'field_1',
					'label' => 'Vorschaubild',
					'name' => 'featured_image',
					'type' => 'image',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'preview_size' => 'thumbnail-portrait',
					'library' => 'all',
					'min_width' => '',
					'min_height' => '',
					'min_size' => '',
					'max_width' => '',
					'max_height' => '',
					'max_size' => '',
					'mime_types' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'taxonomy',
						'operator' => '==',
						'value' => 'category',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
		));

	endif;
});


/**
 * Disable selection of categories with child categories
 */
add_action('added_term_relationship', function ($object_id, $tt_id, $taxonomy) {
	if ('category' != $taxonomy) {
		return;
	}
	$term = get_term_by('term_taxonomy_id', $tt_id, 'category');
	$child_term_ids = get_categories(array(
		'parent' => $term->term_id,
		'fields' => 'ids'
	));
	$is_childless = count($child_term_ids) == 0;

	if ($is_childless) {
		return;
	}

	// Show an error message on the redirect
	add_filter('redirect_post_location', function ($location) {
		return add_query_arg('parent_category_selected', 1, $location);
	});
	// ... and remove the category from the post
	wp_remove_object_terms($object_id, $term->term_id, 'category');
}, 10, 3);

/**
 * Show error message when parent category got selected
 */
add_action('admin_notices', function () {
	if (isset($_GET['parent_category_selected'])) {
	?>
		<div class="notice notice-warning is-dismissible">
			<p>Du kannst dem Beitrag keine Kategorie zuordnen, die eine Unterkategorie enthält. Diese wurde automatisch wieder entfernt.</p>
		</div>
<?php }
});
add_filter('removable_query_args', function ($args) {
	$args[] = 'parent_category_selected';
	return $args;
});

/**
 * FAQs 
 */

// Register custom post type
include_once get_stylesheet_directory() . '/lib/faqs/faq-cpt.php';

// Register custom taxonomy (categories)
include_once get_stylesheet_directory() . '/lib/faqs/faq-category.php';

// Add custom query var to search FAQs
function jw_query_vars_faq_search($vars)
{
	$vars[] = 'faq_search';
	return $vars;
}
add_filter('query_vars', 'jw_query_vars_faq_search');
