<?php

// Avoid the output buffer.
ob_end_flush();
ob_implicit_flush();

$found_posts = get_posts(
	array(
		'numberposts' => -1,
		'post_status' => 'any',
		's'           => 'Affiliatelink/Werbelink',
		'sentence'    => true,
	)
);

$progressbar = \WP_CLI\Utils\make_progress_bar( 'Replacing post content', count( $found_posts ) );

foreach ( $found_posts as $found_post ) {
	$old_content = $found_post->post_content;
	$new_content = preg_replace( '/(&nbsp;\r?\n\r?\n)*\*.*Affiliatelink\/Werbelink.*/', '', $old_content );
	$success     = wp_update_post(
		array(
			'ID'           => $found_post->ID,
			'post_content' => $new_content,
		)
	);

	$progressbar->tick();

	if ( ! $success ) {
		WP_CLI::error( "Failed on post with ID {$found_post->ID}" );
	}
}

$progressbar->finish();
