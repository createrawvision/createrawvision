jQuery(window).load(function(){
	
});
jQuery(document).ready(function() {
	
	/* chosen select */
	jQuery(".chosen-select").chosen({
		disable_search_threshold: 10
	});
	
	jQuery(document).on('click','.wpb-notification-bar-close',function(){
		jQuery('#wpb-notification-bar').fadeOut();
		jQuery.ajax({
			url: ajaxurl,
			data: "action=wpb_close_notification",
			type: 'POST',
			success:function(data){
				
			}
		});
	});

	jQuery('#wpb-notification-bar').animate({'bottom':0},2000,'swing');
	jQuery('#wpb-bookmark-add-collection').on('click', function(e){ 
		jQuery.ajax({
			url: ajaxurl,
			data: "action=wpb_add_new_collection",
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				jQuery('#wpb-bookmark-add-collection').hide();
				jQuery('#wpb-bookmark-add-collection').after(data.html);
				jQuery('#new-collection-title').focus();
				jQuery('#wpb-bookmark-add-collection-cancel').show();
				jQuery('#new-collection-save').on('click',wpb_collection_save);
			} 
		});
		jQuery('#wpb-bookmark-add-collection-cancel').on('click',function(){ location.reload(); });
	});

	function wpb_collection_save(t){
		var collection_privacy = jQuery("input[name=new-collection-privacy]:checked").val();
		var collection_title = jQuery('#new-collection-title').val();
		
		if( collection_title != "" ){
			var collection_id = collection_title.replace(/ /g,"_");
			jQuery.ajax({
				url: ajaxurl,
				data: "action=wpb_save_new_collection&collection_id="+collection_id+"&collection_title="+collection_title+"&collection_privacy="+collection_privacy,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
					location.reload();
				},
				error:function(){
					alert("Collection already exists");
				}
			});
		}
		else{
			alert('Please enter the title for collection');
			jQuery("#new-collection-title").focus();
		}
		
	}
		
	jQuery(".collection_delete_btn").click( function(){
		var result;
		result = window.confirm("Are you sure you want to delete this collection ?");
		if(result){
			wpb_delete_collection(this);
		}
	});
	
	function wpb_delete_collection(t){
		var collection_id = jQuery(t).parent().find('#collection_id').val();
		jQuery.ajax({
			url: ajaxurl,
			data: "action=wpb_delete_collection&collection_id="+collection_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				location.reload();				
			} 
		});
	}
	
});