<?php

add_action(
	'genesis_entry_content',
	function() {
		global $wpb;
		echo wp_kses_post( $wpb->top_bookmarks() );
	}
);

genesis();
