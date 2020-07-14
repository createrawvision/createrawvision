<?php
/*
Plugin Name: User Bookmarks for WordPress
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: This plugin allows your users to bookmark (favorite) any content into private collections.
Version: 3.4
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

define('wpb_url',plugin_dir_url(__FILE__ ));
define('wpb_path',plugin_dir_path(__FILE__ ));

	/* init */
	function wpb_init() {
		load_plugin_textdomain('wpb', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	add_action('init', 'wpb_init');

	/* functions */
	foreach (glob(wpb_path . 'functions/*.php') as $filename) { require_once $filename; }

	/* administration */
	if (is_admin()){
		foreach (glob(wpb_path . 'admin/*.php') as $filename) { include $filename; }
	}
require_once(dirname(__FILE__)."/admin/class-wp-bookmarks-updater.php");
$envato_code_display = wpb_get_option('bookmarks_envato_purchase_code');
if($envato_code_display != false && get_transient('wp_bookmarks_update_check_flag') != 'checked_update'){
	new wp_bookmarks_updater($envato_code_display, plugin_basename(__FILE__) );
	set_transient('wp_bookmarks_update_check_flag', 'checked_update' , 7*24*60*60);
}
