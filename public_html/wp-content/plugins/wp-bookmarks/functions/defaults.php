<?php

	/* get a global option */
	function wpb_get_option( $option ) {
		$wpb_default_options = wpb_default_options();
		$settings = get_option('wpb');
		switch($option){
		
			default:
				if (isset($settings[$option])){
					return $settings[$option];
				} else {
					return $wpb_default_options[$option];
				}
				break;
	
		}
	}
	
	/* set a global option */
	function wpb_set_option($option, $newvalue){
		$settings = get_option('wpb');
		$settings[$option] = $newvalue;
		update_option('wpb', $settings);
	}
	
	/* default options */
	function wpb_default_options(){
		$array['wp_bookmark_popup_type'] = '0';
		$array['wpb_show_sharebutton'] = '1';
		$array['width'] = '300px';
		$array['align'] = 'left';
		$array['inline'] = 0;
		$array['no_top_margin'] = 0;
		$array['no_bottom_margin'] = 0;
		$array['pct_gap'] = 5;
		$array['px_gap'] = 20;
		$array['widgetized'] = 0;
		$array['bookmark_hearticon'] = 1;
		$array['wpb_bookmark_category'] = 0;
		$array['wpb_add_collections'] = 1;
		$array['remove_bookmark'] = __('Remove Bookmark','wpb');
		$array['dialog_bookmarked'] = __('Thanks for bookmarking this content!','wpb');
		$array['dialog_unbookmarked'] = __('This content is no longer in your bookmarks.','wpb');
		$array['default_collection'] = __('Default Collection','wpb');
		$array['add_to_collection'] = __('Add to Collection','wpb');
		$array['new_collection'] = __('New Collection','wpb');
		$array['new_collection_placeholder'] =  __('Enter collection name...','wpb');
		$array['add_new_collection'] = __('Add New Collection','wpb');
		$array['bookmark_category'] = __('Bookmark Category','wpb');
		$array['remove_bookmark_category'] = __('Remove Category Bookmark','wpb');				
		$array['allow_multiple_bookmarks'] = 0;
		$array['auto_bookmark'] = 0;
		$array['include_post_types'] = 'post';
		$array['exclude_ids'] = '';
		$array['bookmarks_envato_purchase_code'] = '';
		$array['wpb_show_users_avatar'] = 0;
		$array['wpb_new_collection_limit']='10';
		$array['wpb_bookmarks_limit']='10';
		
		return apply_filters('wpb_default_options_array', $array);
	}
