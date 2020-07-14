
function wpb_bm_dialog(elem, html, position){
	if (!position){ position = 'left'; }

	if (html == 'new_collection'){
	
		elem.append('<div class="wpb-bm-dialog bm-'+position+'"></div><div class="wpb-bm-dialog-icon bm-'+position+'"><i class="wpb-icon-caret-up"></i></div>');
		elem.find('.wpb-bm-dialog').width( elem.parents('.wpb-bm').width() - 42 );
		custom_html = '<form action="" method="post"><input type="text" name="wpb_bm_new" id="wpb_bm_new" value="" class="wpb-bm-input" placeholder="' + elem.parents('.wpb-bm').data('new_collection_placeholder') + '" /><input type="hidden" class="wpb_bm_radio" name="public" value="private" checked=checked/><div class="wpb-bm-btn-contain bm-block"><a href="#" class="wpb-bm-btn" data-action="submit_collection">' + elem.parents('.wpb-bm').data('add_new_collection') + '</a></div></form>';
	
	} else {
	
		elem.append('<div class="wpb-bm-dialog bm-'+position+' autoclose"></div><div class="wpb-bm-dialog-icon bm-'+position+' autoclose"><i class="wpb-icon-caret-up"></i></div>');
		elem.find('.wpb-bm-dialog').width( elem.parents('.wpb-bm').width() - 42 );
		custom_html = html;
	
	}
	elem.find('.wpb-bm-dialog').html('<span class="wpb-bm-dialog-content">' + custom_html + '</span>');
	
	if (jQuery('#wpb_bm_new').length) jQuery('#wpb_bm_new').focus();
	
	var timer = setTimeout(function(){ jQuery('.wpb-bm-dialog.autoclose,.wpb-bm-dialog-icon.autoclose').hide().remove(); }, 3000);
	
}

function wpb_bm_limitreached_dialog(elem, limit_condition, position, htmltext){
	if(limit_condition == 'coll_limit_reached'){
		elem.append('<div class="wpb-bm-dialog bm-'+position+'"></div><div class="wpb-bm-dialog-icon bm-'+position+'"><i class="wpb-icon-caret-up"></i></div>');
		elem.find('.wpb-bm-dialog').width( elem.parents('.wpb-bm').width() - 42 );
		custom_html = htmltext;
		
	}else if(limit_condition == 'bm_coll_limit_reached'){
		elem.append('<div class="wpb-bm-dialog bm-'+position+'"></div><div class="wpb-bm-dialog-icon bm-'+position+'"><i class="wpb-icon-caret-up"></i></div>');
			
		elem.find('.wpb-bm-dialog').width( elem.parents('.wpb-bm').width() - 42 );
		custom_html = htmltext;
	}
	
	jQuery('.wpb-bm-dialog').click(function(){
		jQuery('.wpb-bm-dialog,.wpb-bm-dialog-icon').fadeOut(2000);
		elem.find('.stop').removeClass('stop');
	});
	elem.find('.wpb-bm-dialog').html('<span class="wpb-bm-dialog-content">' + custom_html + '</span>');
}

function wpb_bm_newaction( elem, parent ) {
	elem.addClass('stop');
	jQuery('.wpb-bm-dialog,.wpb-bm-dialog-icon').hide().remove();
}

function wpb_bm_donebookmark( elem, html ) {
	elem.addClass('bookmarked').removeClass('unbookmarked').removeClass('stop');
	elem.html( html );
}

function wpb_bm_addbookmark( elem, html ) {
	elem.addClass('unbookmarked').removeClass('bookmarked').removeClass('stop');
	elem.html( html );
}

function wpb_bm_updatecount( elem, html ) {
	elem.parents('.wpb-bm').find('.userpro-bm-count').html(html);
}

function wpb_bm_removedialog() {
	jQuery('.wpb-bm-dialog,.wpb-bm-dialog-icon').hide().remove();
}

function wpb_bm_update_active_collection( parent, value ){
	parent.find('input:hidden#collection_id').val( value );
}

/********Code Added By Vipin For Category Bookmark***********/
function wpb_bm_donebookmark_category( elem, html ) {
	elem.addClass('bookmarked_category').removeClass('unbookmarked_category').removeClass('stop');
	elem.html( html );
}

function wpb_bm_addbookmark_category( elem, html ) {
	elem.addClass('unbookmarked_category').removeClass('bookmarked_category').removeClass('stop');
	elem.html( html );
}


function wpb_init_gridlayout(){
	
	jQuery("img").load(function(){
		var grid_container = jQuery('.wpb-grid'); 
		grid_container.isotope({
	    	itemSelector: '.wpb-item.active',
	    	layoutMode: 'masonry',
	    	masonry: {
	    		gutter: 10,
	    	}
		}); 
	});
}

/*********Code Ended***************/

/* Custom JS starts here */
jQuery(document).ready(function() {
	
	wpb_init_gridlayout();
	
	/* code added for bookmark list's grid layout */
	jQuery(document).on('click', '.wpb-button', function(e){
		
		var id = jQuery(this).attr('id');
		var curr_id = jQuery('.wpb-button.active').attr('id');
		jQuery('.wpb-button').removeClass("active");
		jQuery('#'+id).addClass("active");
		jQuery('.wpb-grid .collection_'+curr_id).removeClass("active");
		
		if(!jQuery(this).hasClass('visited')){
		
			jQuery.ajax({
				url: wpb_ajax_url,
				data: 'action=wpb_grid_print_bookmark&collection_id='+id,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
					jQuery('.wpb-loader.loading').show();
					jQuery('.wpb-grid').isotope('destroy');
					jQuery('#'+id).addClass("visited");
					jQuery('.wpb-grid .collection_'+curr_id).hide();
					jQuery('.wpb-grid').append(data.html);
					wpb_init_gridlayout();
				},
				complete:function(){
					jQuery('.wpb-loader.loading').hide();
				}
			});
		}else{
			jQuery('.wpb-loader.loading').show();
			jQuery('.wpb-grid').isotope('destroy');
			jQuery('.wpb-grid .wpb-item.active').removeClass('active');
			jQuery('.wpb-grid .collection_'+curr_id).hide();
			jQuery('.wpb-grid .collection_'+id).addClass('active');
			jQuery('.wpb-grid .collection_'+id).show();
			jQuery('.wpb-grid').isotope({
		    	itemSelector: '.wpb-item.active',
		    	layoutMode: 'masonry',
		    	masonry: {
		    		gutter: 10,
		    	}
			});
			jQuery('.wpb-loader.loading').hide();
		}	
	});
	
	/* Remove bookmark from grid layout */
	jQuery(document).on('click', '.wpb-action-remove i', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.wpb-single');
		post_id = elem.data('post_id');
		collection_id = elem.data('collection_id');
		category_id = '';
		jQuery(this).parents('.wpb-single').fadeOut('fast');
		
		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_removebookmark&post_id='+post_id+'&collection_id='+collection_id+'&category_id='+category_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				// location.reload();
			}
		});
		return false;
	});
	
	jQuery(document).on('click', '.wpb-coll a,.wpb-bm', function(e){

	});
	
	/* remove a collection */
	jQuery(document).on('click', '.wpb-remove-collection', function(e){
		e.preventDefault();
		element = jQuery(this).parents('.wpb-coll-count');
		if (element.find('.wpb-coll-remove').is(':hidden')){
		jQuery(this).html( jQuery(this).data('undo') );
		element.find('.wpb-coll-remove').slideToggle();
		} else {
		jQuery(this).html( jQuery(this).data('remove') );
		element.find('.wpb-coll-remove').slideToggle();
		}
		return false;
	});
	
	/* remove a collection */
	jQuery(document).on('click', '.wpb-hard-remove', function(e){
		e.preventDefault();
		collection_id = jQuery(this).data('collection_id');

		/* switch tab */
		list = jQuery(this).parents('.wpb-coll').find('.wpb-coll-list');
		
		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_hard_remove_collection&collection_id='+collection_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				list.find('a.active').remove();
				list.find('a:first').trigger('click');
			}
		});
		return false;
	});
	
	/* soft-remove a collection */
	jQuery(document).on('click', '.wpb-soft-remove', function(e){
	
		e.preventDefault();
		collection_id = jQuery(this).data('collection_id');

		/* switch tab */
		list = jQuery(this).parents('.wpb-coll').find('.wpb-coll-list');
		
		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_soft_remove_collection&collection_id='+collection_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				list.find('a.active').remove();
				list.find('a:first').trigger('click');
			}
		});
		return false;
	});
	/* Switch a collection */
	jQuery(document).on('click', '.wpb-coll-listpublic a', function(e){
		e.preventDefault();
		collection_id = jQuery(this).data('collection_id');
		user_id = jQuery(this).data('userid_id');
		container = jQuery(this).parents('.wpb-coll').find('.wpb-coll-body');
		if (container.hasClass('loading') == false){

		/* switch tab */
		list = jQuery(this).parents('.wpb-coll-list');
		list.find('a').removeClass('active');
		list.find('a').find('i').addClass('wpb-coll-hide');
		list.find('a').find('span').removeClass('wpb-coll-hide');
		jQuery(this).addClass('active');
		jQuery(this).find('i').removeClass('wpb-coll-hide');
		jQuery(this).find('span').addClass('wpb-coll-hide');
		
		container.addClass('loading').find('.wpb-coll-body-inner').find('div:not(.wpb-coll-remove)').fadeTo(0, 0);
		
		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_change_public_collection&collection_id='+collection_id+'&user_id='+user_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				container.removeClass('loading').find('.wpb-coll-body-inner').empty().html(data.res);
			}
		});
		
		}
		return false;
	});	


	
	/* Switch a collection */
	jQuery(document).on('click', '.wpb-coll-list a', function(e){
		e.preventDefault();
		collection_id = jQuery(this).data('collection_id');
		container = jQuery(this).parents('.wpb-coll').find('.wpb-coll-body');
		if (container.hasClass('loading') == false){

		/* switch tab */
		list = jQuery(this).parents('.wpb-coll-list');
		list.find('a').removeClass('active');
		list.find('a').find('i').addClass('wpb-coll-hide');
		list.find('a').find('span').removeClass('wpb-coll-hide');
		jQuery(this).addClass('active');
		jQuery(this).find('i').removeClass('wpb-coll-hide');
		jQuery(this).find('span').addClass('wpb-coll-hide');
		
		container.addClass('loading').find('.wpb-coll-body-inner').find('div:not(.wpb-coll-remove)').fadeTo(0, 0);
		
		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_change_collection&collection_id='+collection_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				
				   
					
				container.removeClass('loading').find('.wpb-coll-body-inner').empty().html(data.res);
				 var n = jQuery(".a2a_default_style").length;
				
				for (i = 0; i < n ; i++) {
   				 a2a.init();
				}
			}
		});
		
		}
		return false;
	});
	
	/* Disable forms */
	jQuery(document).on('submit', '.wpb-bm form', function(e){
		e.preventDefault();
		return false;
	});

	/* Capture change in collection */
	jQuery(document).on('change', '.wpb-bm-list select', function(e){
		dd = jQuery(this);
		var parent = dd.parents('.wpb-bm');
		bookmarked_link = parent.find('a.bookmarked');
		bookmarked_category_link=dd.parents('.wpb-bm').find('a.bookmarked_category');
		unbookmarked_category_link=dd.parents('.wpb-bm').find('a.unbookmarked_category');
		unbookmarked_link = parent.find('a.unbookmarked');
		collection_id = parent.find('input:hidden#collection_id').val();
		post_id = parent.data('post_id');
		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_checkifbookmarked&collection_id='+dd.val()+'&post_id='+post_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if(data.status){
					wpb_bm_donebookmark( unbookmarked_link, parent.data('remove_bookmark') );
					wpb_bm_donebookmark_category( unbookmarked_category_link, dd.parents('.wpb-bm').data('remove_bookmark_category') );
					}
				else{
					wpb_bm_addbookmark( bookmarked_link, parent.data('add_to_collection') );
					wpb_bm_addbookmark_category( bookmarked_category_link, dd.parents('.wpb-bm').data('bookmark_category') );
				}
			}
		})
//		if (dd.val() != collection_id){
//			wpb_bm_addbookmark( bookmarked_link, parent.data('add_to_collection') );
//			wpb_bm_addbookmark_category( bookmarked_category_link, dd.parents('.wpb-bm').data('bookmark_category') );
//		} else {
//			wpb_bm_donebookmark( unbookmarked_link, parent.data('remove_bookmark') );
//			wpb_bm_donebookmark_category( unbookmarked_category_link, dd.parents('.wpb-bm').data('remove_bookmark_category') );
//		}
	});

	/* trigger submit new collection */
	jQuery(document).on('click', '.wpb-bm-dialog a[data-action="submit_collection"]', function(e){
		e.preventDefault();
		jQuery(this).parents('form').trigger('submit');
	});
	
	/* submit new collection */
	jQuery(document).on('submit', '.wpb-bm-dialog form:not(.stop)', function(e){
		e.preventDefault();
		elem = jQuery(this);
		dialog = jQuery(this).parents('.wpb-bm-dialog');
		var parent = jQuery(this).parents('.wpb-bm');
		
		collection_name = dialog.find('#wpb_bm_new').val();
		privacy = dialog.find('input[name=public]:checked').val();
		if (collection_name != ''){
		
		elem.addClass('stop');
		default_collection = parent.data('default_collection');
		post_id = parent.data('post_id');
		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_addcollection&post_id='+post_id+'&default_collection='+default_collection+'&collection_name='+collection_name+'&privacy='+privacy,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if(typeof(data.errors) == "undefined" && data.errors == null){
					elem.removeClass('stop');
					parent.find('#wpb_bm_collection').replaceWith( data.options );
					parent.find("select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
					parent.find("*[class*=chzn], .chosen-container").remove();
					jQuery(".chosen-select-collections").chosen({
						disable_search_threshold: 10,
						width: '100%'
					});
					parent.find('#wpb_bm_collection').val( parent.find('#wpb_bm_collection option:last').val() ).trigger("chosen:updated");
					parent.find('.wpb-bm-list select').trigger('change');
					wpb_bm_removedialog();
				}else{
					var cur_elem = elem.parents('.wpb-bm-act').find('.wpb-bm-btn-contain.bm-right');
					wpb_bm_removedialog();
					wpb_bm_limitreached_dialog( cur_elem, 'coll_limit_reached', 'right', data.errors);
				}
			}
		});
		
		}
		return false;
	});
	
	/* chosen jquery */
	jQuery(".chosen-select-collections").chosen({
		disable_search_threshold: 10,
		width: '100%'
	});

	/* New collection */
	jQuery(document).on('click', '.wpb-bm a[data-action=newcollection]', function(e){
		e.preventDefault();
		elem = jQuery(this);
		var parent = jQuery(this).parents('.wpb-bm');
		
		if ( parent.find('.wpb-bm-dialog form').length == 0){
			wpb_bm_newaction( elem, parent );
			elem.addClass('active');
			wpb_bm_dialog( elem.parent(), 'new_collection', 'right' );
		} else {
			elem.removeClass('active');
			wpb_bm_removedialog();
		}
		return false;
	});

	/* New bookmark */
	jQuery(document).on('click', '.wpb-bm a[data-action=bookmark].unbookmarked:not(.stop)', function(e){
		elem = jQuery(this);
		var parent = jQuery(this).parents('.wpb-bm');
		post_id = parent.data('post_id');
		collection_id = parent.find('#wpb_bm_collection').val();
		
		wpb_bm_newaction( elem, parent );

		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_newbookmark&post_id='+post_id+'&collection_id='+collection_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if(typeof(data.errors) == "undefined" && data.errors == null){
					wpb_bm_updatecount(elem, data.updated_count);
					wpb_bm_update_active_collection( parent, data.collection_id );
					wpb_bm_donebookmark( elem, parent.data('remove_bookmark') );
					wpb_bm_dialog( elem.parent(), parent.data('dialog_bookmarked') );
					jQuery("#"+post_id).removeClass('unbookmark').addClass('addedbookmark');
					// jQuery('.wppopup').html('bookmarked');
					jQuery('.wppopup > .fa-heart-o').removeClass('fa-heart-o').addClass('fa-heart');
				}else{
					
					wpb_bm_limitreached_dialog( elem.parent(), 'bm_coll_limit_reached', 'right', data.errors);
					
				}
				
			}
		});
		return false;
		
	});
	
	/* Remove bookmark */
	jQuery(document).on('click', '.wpb-bm a[data-action=bookmark].bookmarked:not(.stop)', function(e){
		elem = jQuery(this);
		var parent = jQuery(this).parents('.wpb-bm');
		post_id = parent.data('post_id');
		collection_id = parent.find('#wpb_bm_collection').val();
		/***************************Code added for category bookmark*************************************************/
		category_id=parent.data('category_id');
		/***************************Code End********************************************************************/
		
		wpb_bm_newaction( elem, parent );

		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_removebookmark&post_id='+post_id+'&collection_id='+collection_id+'&category_id='+category_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				wpb_bm_addbookmark( elem, parent.data('add_to_collection') );
				wpb_bm_dialog( elem.parent(), parent.data('dialog_unbookmarked') );
				// location.reload();
			}
		});
		return false;
		
	});
	
	/* Remove bookmark */
	jQuery(document).on('click', 'a.wpb-coll-abs', function(e){
		elem = jQuery(this);
		var parent = jQuery(this).parents('.wpb-coll-item');
		post_id = elem.data('post_id');
		collection_id = elem.data('collection_id');
		category_id = elem.data('category_id');

		parent.fadeOut('fast');
		
		jQuery.ajax({
			url: wpb_ajax_url,
			data: 'action=wpb_removebookmark&post_id='+post_id+'&collection_id='+collection_id+'&category_id='+category_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				// location.reload();
			}
		});
		return false;

	});

/*******************************************************Code Added By Vipin for category bookmarks*******************************************************************/
		/* New Category Bookmark */
		jQuery(document).on('click', '.wpb-bm a[data-action=bookmarkcategory].unbookmarked_category:not(.stop)', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.wpb-bm');
  		category_id = jQuery(this).parents('.wpb-bm').data('category_id');
		
  		collection_id = jQuery(this).parents('.wpb-bm').find('#wpb_bm_collection').val();
                remove_bookmark_category = jQuery(this).parents('.wpb-bm').data('remove_bookmark_category');
                dialog_bookmarked = jQuery(this).parents('.wpb-bm').data('dialog_bookmarked');
		post_id=jQuery(this).parents('.wpb-bm').data('post_id');
 	 	wpb_bm_newaction( elem, jQuery(this).parents('.wpb-bm') );
		if(typeof(category_id)=='string')
		{
			var category_list=category_id.split(",");
			for(i=0;i<(category_list.length-1);i++)
			{
				if(jQuery(this).data('category')==category_list[i])
				{
					jQuery.ajax({
						url: wpb_ajax_url,
						data: 'action=wpb_newcategorybookmark&category_id='+jQuery(this).data('category')+'&collection_id='+collection_id+'&post_id='+post_id,
						dataType: 'JSON',
						type: 'POST',
						success:function(data){
    							wpb_bm_update_active_collection( jQuery(this).parents('.wpb-bm'), data.collection_id );
    							wpb_bm_donebookmark_category( elem , remove_bookmark_category );
							wpb_bm_dialog( elem.parent(), dialog_bookmarked );
							// location.reload();
						}
					});
				}
			}
		}
		else
		{
			jQuery.ajax({
				url: wpb_ajax_url,
				data: 'action=wpb_newcategorybookmark&category_id='+category_id+'&collection_id='+collection_id+'&post_id='+post_id,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
    					wpb_bm_update_active_collection( jQuery(this).parents('.wpb-bm'), data.collection_id );
    					wpb_bm_donebookmark_category( elem , remove_bookmark_category );
					wpb_bm_dialog( elem.parent(), dialog_bookmarked );
					// location.reload();
			}
		});
		}
		
		return false;
		
	});
	
	/* Remove category bookmark */
	jQuery(document).on('click', '.wpb-bm a[data-action=bookmarkcategory].bookmarked_category:not(.stop)', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.wpb-bm');
		category_id = jQuery(this).parents('.wpb-bm').data('category_id');
		collection_id = jQuery(this).parents('.wpb-bm').find('#wpb_bm_collection').val();
		bookmark_category=jQuery(this).parents('.wpb-bm').data('bookmark_category');
                dialog_unbookmarked=jQuery(this).parents('.wpb-bm').data('dialog_unbookmarked');
		wpb_bm_newaction( elem, parent );

		if(typeof(category_id)=='string')
		{
			var category_list=category_id.split(",");
			for(i=0;i<(category_list.length-1);i++)
			{
				if(jQuery(this).data('category')==category_list[i])
				{
					jQuery.ajax({
						url: wpb_ajax_url,
						data: 'action=wpb_removecategorybookmark&category_id='+jQuery(this).data('category')+'&collection_id='+collection_id,
						dataType: 'JSON',
						type: 'POST',
						success:function(data){
    							wpb_bm_addbookmark_category( elem , bookmark_category );
							wpb_bm_dialog( elem.parent(), dialog_unbookmarked );
							// location.reload();
						}
					});
				}
			}
		}
		
		else
		{
			jQuery.ajax({
				url: wpb_ajax_url,
				data: 'action=wpb_removecategorybookmark&category_id='+category_id+'&collection_id='+collection_id,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
    					wpb_bm_addbookmark_category( elem , bookmark_category );
					wpb_bm_dialog( elem.parent(), dialog_unbookmarked );
					// location.reload();
				}
			});
		}

		return false;
		
	});
/*************************Code Ended********************************************************************************************/
	
});


function wpb_bookmark_icon(post_id,elm){
	
	var condition = jQuery(elm).attr('id');
	str = 'action=wpb_bookmark_icon&post_id='+post_id+'&condition='+condition;
	jQuery.ajax({
		url: wpb_ajax_url,
		data: str,
		type: 'POST',
		success:function(data){
			// location.reload();
		}
	});
}