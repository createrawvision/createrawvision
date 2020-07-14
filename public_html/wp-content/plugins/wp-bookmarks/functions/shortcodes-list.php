<?php

	/* Registers and display the shortcode */
	add_shortcode('collections', 'collections' );
	function collections( $args=array() ) {
		global $wpb;

		/* arguments */
		$defaults = array(

		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		
		if( isset($args['layout']) && $args['layout'] == 'grid' ){
			include_once (wpb_path.'templates/template-grid-layout.php');
		}else{
			return $wpb->bookmarks( $args );
		}
		
		
	
	}
	add_shortcode('publiccollections', 'publiccollections' );
	function publiccollections( $args=array() ) {
		global $wpb;

		/* arguments */
		$defaults = array(

		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		return $wpb->public_bookmarks( $args );
	
	}
