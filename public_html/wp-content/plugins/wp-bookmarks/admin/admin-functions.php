<?php

	function wpb_admin_fav_get_posttypes(){
		$types = get_post_types( array('public' => true) , 'objects');
		foreach($types as $type){
			$array[$type->name] = $type->labels->menu_name;
		}
		return $array;
	}
	
	/* gets a selected value */
	function wpb_is_selected($k, $arr){
		if (isset($arr) && is_array($arr) && in_array($k, $arr)) {
			echo 'selected="selected"';
		} elseif ( $arr == $k ) {
			echo 'selected="selected"';
		}
	}