<?php

if (!defined('WP_CLI') || !WP_CLI) {
  echo "WP_CLI not defined";
  exit;
}

// Check the version to deploy all needed changes
$version_option_name = 'crv_version';
$version = get_option($version_option_name);

if (!$version) {
  $version = '0.0.0';
  add_option($version_option_name, $version);
}

// Changes for version 0.0.1-dev
$new_version = '0.0.1-dev';
if (version_compare($version, $new_version, '<')) {
  // Insert post into database
  $post_id = wp_insert_post(array(
    'post_type' => 'page',
    'post_title' => 'Deployment Test',
    'post_content' => 'Wenn diese Seite als Entwurf sichtbar ist, dann hat alles geklappt. Glückwunsch!'
  ));

  // Roll back changes on failed deployment
  if ($post_id == 0) {
    echo "Deployment for version $new_version failed";
    exit;
  }

  update_option($version_option_name, $new_version);
}

echo 'Deployment complete';
exit;
