<?php

	/* Registers and display the shortcode */
	add_shortcode( 'bookmark', 'bookmark' );
function bookmark( $args = array() ) {
	global $wpb;

	/* arguments */
	$defaults = array(
		'width'                      => wpb_get_option( 'width' ),
		'align'                      => wpb_get_option( 'align' ),
		'inline'                     => wpb_get_option( 'inline' ),
		'no_top_margin'              => wpb_get_option( 'no_top_margin' ),
		'no_bottom_margin'           => wpb_get_option( 'no_bottom_margin' ),
		'pct_gap'                    => wpb_get_option( 'pct_gap' ),
		'px_gap'                     => wpb_get_option( 'px_gap' ),
		'widgetized'                 => wpb_get_option( 'widgetized' ),
		'remove_bookmark'            => wpb_get_option( 'remove_bookmark' ),
		'dialog_bookmarked'          => wpb_get_option( 'dialog_bookmarked' ),
		'dialog_unbookmarked'        => wpb_get_option( 'dialog_unbookmarked' ),
		'default_collection'         => wpb_get_option( 'default_collection' ),
		'add_to_collection'          => wpb_get_option( 'add_to_collection' ),
		'new_collection'             => wpb_get_option( 'new_collection' ),
		'new_collection_placeholder' => wpb_get_option( 'new_collection_placeholder' ),
		'add_new_collection'         => wpb_get_option( 'add_new_collection' ),
		'bookmark_category'          => wpb_get_option( 'bookmark_category' ),
		'remove_bookmark_category'   => wpb_get_option( 'remove_bookmark_category' ),
	);
	$args     = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	return $wpb->bookmark( $args );

}
