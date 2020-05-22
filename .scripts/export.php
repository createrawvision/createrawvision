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


/**
 * Save ACF local fields for teaser text and image for member posts
 */

$teaser_fields = array_keys(array_filter(acf_get_local_fields(), function ($field) {
  return $field['parent'] == 'group_5ea95be98a61e';
}));

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

file_put_contents(
  ABSPATH . '../deployment_data/teaser-data-' . date('Y-m-d\TH-i-s') . '.json',
  json_encode($teaser_data, JSON_PRETTY_PRINT)
);
