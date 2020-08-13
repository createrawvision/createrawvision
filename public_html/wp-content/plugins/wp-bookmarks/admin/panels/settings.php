<form method="post" action="">

<h3><?php _e('General Settings','wpb'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="bookmarks_envato_purchase_code"><?php _e('Envato Purchase code','wpb'); ?></label></th>
		<td>
			<input type="text" name="bookmarks_envato_purchase_code" id="bookmarks_envato_purchase_code" value="<?php echo wpb_get_option('bookmarks_envato_purchase_code'); ?>" class="regular-text" />
			
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="auto_bookmark"><?php _e('Automatically add bookmark widget after post content','wpb'); ?></label></th>
		<td>
			<select name="auto_bookmark" id="auto_bookmark" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('auto_bookmark')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('auto_bookmark')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="wp_bookmark_popup_type"><?php _e('Bookmark Widget Type','wpb'); ?></label></th>
		<td>
			<select name="wp_bookmark_popup_type" id="wp_bookmark_popup_type" class="chosen-select" style="width:300px">
				<option value="0" <?php selected('0', wpb_get_option('wp_bookmark_popup_type')); ?>><?php _e('Widget','wpb'); ?></option>
				<option value="1" <?php selected('1', wpb_get_option('wp_bookmark_popup_type')); ?>><?php _e('Popup Using a Bookmark Button','wpb'); ?></option>
				<option value="2" <?php selected('2', wpb_get_option('wp_bookmark_popup_type')); ?>><?php _e('Heart Icon','wpb'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="allow_multiple_bookmarks"><?php _e('Allow posts to be bookmarked in multiple collections','wpb'); ?></label></th>
		<td>
			<select name="allow_multiple_bookmarks" id="allow_multiple_bookmarks" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('allow_multiple_bookmarks')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('allow_multiple_bookmarks')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="bookmark_hearticon"><?php _e('Display heart icon on the bookmark widget','wpb'); ?></label></th>
		<td>
			<select name="bookmark_hearticon" id="bookmark_hearticon" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('bookmark_hearticon')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('bookmark_hearticon')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="wpb_show_users_avatar"><?php _e('Display avatar of the user who has bookmarked the post','wpb'); ?></label></th>
		<td>
			<select name="wpb_show_users_avatar" id="wpb_show_users_avatar" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('wpb_show_users_avatar')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('wpb_show_users_avatar')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="include_post_types[]"><?php _e('Enable these post types','wpb'); ?></label></th>
		<td>
			<select name="include_post_types[]" id="include_post_types[]" multiple="multiple" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Choose post types','wpb'); ?>">
				<?php
				$array = wpb_admin_fav_get_posttypes();
				foreach($array as $k=>$v) {
				?>
				<option value="<?php echo $k; ?>" <?php wpb_is_selected($k, wpb_get_option('include_post_types') ); ?>><?php echo $v; ?></option>
				<?php } ?>
			</select>
			<span class="description"><?php _e('Select here the post types that can be bookmarked (If you enable auto insertion of bookmark widget)','wpb'); ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="exclude_ids"><?php _e('Exclude these IDs','wpb'); ?></label></th>
		<td>
			<input type="text" name="exclude_ids" id="exclude_ids" value="<?php echo wpb_get_option('exclude_ids'); ?>" class="regular-text" />
			<span class="description"><?php _e('For automatic widget insertion, this can exclude the post IDs you specify here (seperated by comma) regardless of any other settings.','wpb'); ?></span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="wpb_bookmark_category"><?php _e('Allow category bookmarking','wpd'); ?></label></th>
		<td>
			<select name="wpb_bookmark_category" id="wpb_bookmark_category" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('wpb_bookmark_category')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('wpb_bookmark_category')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="wpb_add_collections"><?php _e('Allow to create new collections','wpd'); ?></label></th>
		<td>
			<select name="wpb_add_collections" id="wpb_add_collections" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('wpb_add_collections')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('wpb_add_collections')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>	
	<tr valign="top">
		<th scope="row"><label for="wpb_show_sharebutton"><?php _e('Display Social Share button on collection','wpb'); ?></label></th>
		<td>
			<select name="wpb_show_sharebutton" id="wpb_show_sharebutton" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('wpb_show_sharebutton')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('wpb_show_sharebutton')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="wpb_new_collection_limit"><?php _e('Limit the number of new collections to be created','wpb'); ?></label></th>
		<td>
			<input style="width: 35.5%;" type="number" min="1" name="wpb_new_collection_limit" id="wpb_new_collection_limit" value="<?php echo wpb_get_option('wpb_new_collection_limit'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="wpb_bookmarks_limit"><?php _e('Limit the number of posts to be bookmarked in collections','wpb'); ?></label></th>
		<td>
			<input style="width: 35.5%;" type="number" min="1" name="wpb_bookmarks_limit" id="wpb_bookmarks_limit" value="<?php echo wpb_get_option('wpb_bookmarks_limit'); ?>" class="regular-text" />
		</td>
	</tr>
	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wpb'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','wpb'); ?>"  />
</p>

<h3><?php _e('Bookmark Shortcode Settings','wpb'); ?></h3>
<p><?php _e('These settings can be overridden by shortcode options. They are for general bookmark template that appears on your content. The bookmark sidebar widget may override some settings to make it look perfect in sidebar.','wpb'); ?></p>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="width"><?php _e('Bookmark Widget Width','wpb'); ?></label></th>
		<td>
			<input type="text" name="width" id="width" value="<?php echo wpb_get_option('width'); ?>" class="regular-text" />
			<span class="description"><?php _e('e.g. 250px, 50%, 300px, etc.','wpb'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="widgetized"><?php _e('Widgetized Look','wpb'); ?></label></th>
		<td>
			<select name="widgetized" id="widgetized" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('widgetized')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('widgetized')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="align"><?php _e('Default Alignment','wpb'); ?></label></th>
		<td>
			<select name="align" id="align" class="chosen-select" style="width:300px">
				<option value="left" <?php selected('left', wpb_get_option('align')); ?>><?php _e('Left','wpb'); ?></option>
				<option value="right" <?php selected('right', wpb_get_option('align')); ?>><?php _e('Right','wpb'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="inline"><?php _e('Inline Display','wpb'); ?></label></th>
		<td>
			<select name="inline" id="inline" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('inline')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('inline')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="no_top_margin"><?php _e('Disable top margin','wpb'); ?></label></th>
		<td>
			<select name="no_top_margin" id="no_top_margin" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('no_top_margin')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('no_top_margin')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="no_bottom_margin"><?php _e('Disable bottom margin','wpb'); ?></label></th>
		<td>
			<select name="no_bottom_margin" id="no_bottom_margin" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', wpb_get_option('no_bottom_margin')); ?>><?php _e('Yes','wpb'); ?></option>
				<option value="0" <?php selected('0', wpb_get_option('no_bottom_margin')); ?>><?php _e('No','wpb'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="pct_gap"><?php _e('Gap between buttons (%)','wpb'); ?></label></th>
		<td>
			<input type="text" name="pct_gap" id="pct_gap" value="<?php echo wpb_get_option('pct_gap'); ?>" class="regular-text" />
			<span class="description"><?php _e('This is used if your bookmark widget width is fluid using percentages.','wpb'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="px_gap"><?php _e('Gap between buttons (px)','wpb'); ?></label></th>
		<td>
			<input type="text" name="px_gap" id="px_gap" value="<?php echo wpb_get_option('px_gap'); ?>" class="regular-text" />
			<span class="description"><?php _e('This is used if your bookmark widget width is fixed using pixels.','wpb'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="remove_bookmark"><?php _e('Text for "Remove Bookmark"','wpb'); ?></label></th>
		<td>
			<input type="text" name="remove_bookmark" id="remove_bookmark" value="<?php echo wpb_get_option('remove_bookmark'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="dialog_bookmarked"><?php _e('Text for "Bookmark has been added"','wpb'); ?></label></th>
		<td>
			<input type="text" name="dialog_bookmarked" id="dialog_bookmarked" value="<?php echo wpb_get_option('dialog_bookmarked'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="dialog_unbookmarked"><?php _e('Text for "Bookmark has been removed"','wpb'); ?></label></th>
		<td>
			<input type="text" name="dialog_unbookmarked" id="dialog_unbookmarked" value="<?php echo wpb_get_option('dialog_unbookmarked'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="default_collection"><?php _e('Text for "Default Collection"','wpb'); ?></label></th>
		<td>
			<input type="text" name="default_collection" id="default_collection" value="<?php echo wpb_get_option('default_collection'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="add_to_collection"><?php _e('Text for "Add to Collection"','wpb'); ?></label></th>
		<td>
			<input type="text" name="add_to_collection" id="add_to_collection" value="<?php echo wpb_get_option('add_to_collection'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="new_collection"><?php _e('Text for "New Collection"','wpb'); ?></label></th>
		<td>
			<input type="text" name="new_collection" id="new_collection" value="<?php echo wpb_get_option('new_collection'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="new_collection_placeholder"><?php _e('Text for "New Collection Placeholder"','wpb'); ?></label></th>
		<td>
			<input type="text" name="new_collection_placeholder" id="new_collection_placeholder" value="<?php echo wpb_get_option('new_collection_placeholder'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="add_new_collection"><?php _e('Text for "Submit New Collection"','wpb'); ?></label></th>
		<td>
			<input type="text" name="add_new_collection" id="add_new_collection" value="<?php echo wpb_get_option('add_new_collection'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<!----------------------------------------------------------------------------------------------------------------->
	<tr valign="top">
		<th scope="row"><label for="bookmark_category"><?php _e('Text for "Bookmark Category"','wpb'); ?></label></th>
		<td>
			<input type="text" name="bookmark_category" id="bookmark_category" value="<?php echo wpb_get_option('bookmark_category'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="remove_bookmark_category"><?php _e('Text for "Remove Category Bookmark"','wpb'); ?></label></th>
		<td>
			<input type="text" name="remove_bookmark_category" id="remove_bookmark_category" value="<?php echo wpb_get_option('remove_bookmark_category'); ?>" class="regular-text" />
		</td>
	</tr>
<!----------------------------------------------------------------------------------------------------------------->
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wpb'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','wpb'); ?>"  />
</p>

</form>
