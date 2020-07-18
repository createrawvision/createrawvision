<?php
/**
 * Deletes the original image for scaled down images.
 *
 * @link https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/
 * Since WordPress 5.3, it scales down images, which are bigger than 2560 pixels.
 *
 * This script finds all attachments which have a meta field 'original_image',
 * then deletes this meta entry and deletes the original file.
 *
 * The scaled file won't get renamed, since it could be referenced by its name somewhere.
 *
 * 'original_image' is used for this purpose exclusively.
 * Editing images just replaces the original one, even though WordPress keeps the files around.
 *
 * CAUTION: WordPress uses the original image to generate other image sizes.
 * If you delete the original image, all newly generated images get scaled down from the already scaled down image.
 * This could create more artifacts, but most of the time, you can't notice it, really.
 *
 * Regenerating all image sizes will then rename the files to '*-scaled', too,
 * since the original image can't be used as a base for scaling.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

$huge_image_posts = get_posts(
	array(
		'post_type'    => 'attachment',
		'numberposts'  => -1,
		'meta_key'     => '_wp_attachment_metadata',
		'meta_value'   => 'original_image',
		'meta_compare' => 'LIKE',
	)
);

// Confirm deletion.
$count = count( $huge_image_posts );
if ( ! $count ) {
	WP_CLI::log( 'I found no huge images. Exiting...' );
	exit;
}
WP_CLI::confirm( 'I found ' . $count . ' huge images. Would you like me to delete them?' );

// The 'uploads' folder (without trailing slash).
$upload_base_dir = wp_upload_dir()['basedir'];

foreach ( $huge_image_posts as $image_post ) {
	$meta           = $image_post->_wp_attachment_metadata;
	$original_image = $meta['original_image'];
	$file           = trailingslashit( $upload_base_dir ) . $meta['file'];

	if ( ! $original_image ) {
		WP_CLI::warning( "No 'original_image' meta found for post {$image_post->post_title}. Skipping..." );
		continue;
	}

	// Delete 'original_image' meta.
	unset( $meta['original_image'] );
	$success = update_post_meta( $image_post->ID, '_wp_attachment_metadata', $meta );
	if ( ! $success ) {
		WP_CLI::warning( "Failed to update meta for {$image_post->post_title}. Skipping..." );
		continue;
	}

	// Delete the original file.
	$original_file = trailingslashit( dirname( $file ) ) . $original_image;
	$success       = wp_delete_file_from_directory( $original_file, $upload_base_dir );
	if ( ! $success ) {
		WP_CLI::warning( "Failed to delete $original_file" );
	}
}
