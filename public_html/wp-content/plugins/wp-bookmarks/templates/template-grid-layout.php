<?php

global $post, $wpb;
$defaults = array(
	'default_collection' => wpb_get_option( 'default_collection' ),
);
$args     = wp_parse_args( $args, $defaults );

/* The arguments are passed via shortcode through admin panel*/
foreach ( $defaults as $key => $val ) {
	if ( isset( $args[ $key ] ) ) {
		$$key = $args[ $key ];
	} else {
		$$key = $val;
	}
}

/* output */
$output  = '';
$results = 0;
// logged in
if ( is_user_logged_in() ) {

	$collections = $wpb->get_collections( get_current_user_id() );

	?>
<div class="wpb-container">

	<div class="wpb-filter">
	<?php
	foreach ( $collections as $id => $array ) {
		include wpb_path . 'templates/template-grid-filter.php';
	}
	?>
	</div>

	<div class="wpb-grid">
		<div class="wpb-loader loading" style="display: none;"></div>
	<?php
	$id = 0;
	?>

		<div class="wpb-single-bmcount collection_<?php echo intval( $id ); ?>">
			<span>
			<?php
			echo intval( $wpb->get_bookmarks_count_by_collection( $id ) );
				esc_html_e( ' Bookmarks in collection', 'wpb' );
			?>
			</span>
		</div>
	<?php
	$bks = $wpb->get_bookmarks_by_collection( $id );

	if ( is_array( $bks ) ) {
		$bks = array_reverse( $bks, true );

		foreach ( $bks as $bkid => $array ) {
			if ( 'label' !== $bkid && 'privacy' !== $bkid && 'userid' !== $bkid && 'type' !== $bkid ) {
				$results++;
				if ( get_post_status( $bkid ) === 'publish' ) { // active post

					include wpb_path . 'templates/template-single-bookmark.php';

				}
			}
		}
	}
	?>
	</div>
</div>
	<?php

} else {
	$output .= '<p>' . sprintf( esc_html__( 'You need to <a href="%1$s">login</a> or <a href="%2$s">register</a> to view and manage your bookmarks.', 'wpb' ), wp_login_url( get_permalink() ), site_url( '/wp-login.php?action=register&redirect_to=' . get_permalink() ) ) . '</p>';
}// End if().

echo esc_html( $output );
