<?php

/**
 * Exports edits from the current environment
 * 
 * Execute with WP-CLI (and add `--user=Josef` to have enough capabilites)!
 */

if (!defined('WP_CLI') || !WP_CLI) {
  echo "WP_CLI not defined", PHP_EOL;
  exit(1);
}

if (!current_user_can('manage_options')) {
  echo "Insufficient capabilites. Make sure to run the script with admin capabilites (e.g. --user=<admin>).", PHP_EOL;
  exit(1);
}

require_once __DIR__ . '/wp-cli-utils.php';

// Avoid the output buffer
ob_end_flush();
ob_implicit_flush();


export_teaser();


/* 
   ###############
   #             #
   #  FUNCTIONS  #
   #             #
   ###############
*/

/**
 * Save ACF local fields for teaser text and image for member posts in deployment_data/teaser-data-<timestamp>
 */
function export_teaser()
{
  $member_post_ids = get_posts([
    'numberposts' => -1,
    'category_name' => 'member',
    'post_status' => 'any',
    'fields' => 'ids'
  ]);

  $teaser_data = [];

  foreach ($member_post_ids as $post_id) {
    $teaser_data[$post_id] = array_filter([
      'custom_teaser' => get_field('custom_teaser', $post_id),
      'teaser_text' => get_field('teaser_text', $post_id),
      'teaser_image_id' => get_field('teaser_image', $post_id)['id']
    ], function ($v) {
      return !is_null($v);
    });
  }

  $path = __DIR__ . '/../deployment_data/teaser-data.json';
  if (file_exists($path)) {
    $old_teaser_data = json_decode(file_get_contents($path), $assoc = TRUE);
    if (is_array($old_teaser_data))
      $teaser_data = $teaser_data + $old_teaser_data;
  }
  file_put_contents($path, json_encode($teaser_data, JSON_PRETTY_PRINT));
}
