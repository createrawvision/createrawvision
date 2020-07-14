<label for="new-collection-title" style="font-size: 14px;font-weight: bolder;"><?php _e('Collection Title','wpb'); ?></label>
<tr valign="top" class="add-new-collection" style="width:400px;">
	<td>
	<br>
		<input required type="text" style="width:300px !important;" name="new-collection-title" id="new-collection-title" value="" />
	</td>
</tr>
<br>
<label for="new-collection-privacy" style="font-size: 14px;font-weight: bolder;"><?php _e('Collection Privacy','wpb'); ?></label>
<tr valign="top" class="add-new-collection" style="width:400px;">
	<td>
		<br>
		<input type="radio" checked="checked" name="new-collection-privacy" value="public"> <?php _e('Public','wpb');?><br>
		<input type="radio" name="new-collection-privacy" value="private">  <?php _e('Private','wpb');?><br>
	</td>	
</tr>
<br>
<tr valign="top" class="add-new-collection" style="width:400px;">
	<td>
		<input type="button" class="button" style="" name="new-collection-save" value="Save" id="new-collection-save" />
	</td>
</tr>