<?php

if (!defined('WP_CLI') || !WP_CLI) {
  echo "WP_CLI not defined", PHP_EOL;
  exit(1);
}

// Check the version to deploy all needed changes
$version_option_name = 'crv_version';
$version = get_option($version_option_name);

if (!$version) {
  $version = '0.0.0';
  add_option($version_option_name, $version);
}

$new_version = '0.0.1';
if (version_compare($version, $new_version, '<')) {
  echo "Deploying version $new_version", PHP_EOL;

  // Install Restrict Content Pro
  $retval = WP_CLI::runcommand("plugin install ./deployment_data/restrict-content-pro.zip --activate", array(
    'return'     => 'all',
    'exit_error' => false
  ));

  print_r($retval->stdout);
  print_r($retval->stderr);
  if ($retval->return_code !== 0) {
    echo 'Failed to install Restrict Content Pro! Make sure to have it in the same directory it is called restrict-content-pro.zip', PHP_EOL;
    exit(1);
  }

  update_option($version_option_name, $new_version);
}

$new_version = '0.0.2';
if (version_compare($version, $new_version, '<')) {
  echo "Deploying version $new_version", PHP_EOL;

  // Optional: Add License Key (deactivate it in RCP Website first) -> option 'rcp_license_status'

  // Get the final 'rcp_settings' option (with payment gateway!)

  // Insert mebership level

  // Restrict site content (a2kA1_termmeta -> meta_key = rcp_restricted_meta)

  update_option($version_option_name, $new_version);
}

$new_version = '0.1.0';
if (version_compare($version, $new_version, '<')) {
  echo "Deploying version $new_version", PHP_EOL;

  // Install ACF Plugin, if not installed already
  if (WP_CLI::runcommand("plugin is-installed advanced-custom-fields", ['return' => 'return_code', 'exit_error' => false]) != 0) {
    WP_CLI::runcommand("plugin install advanced-custom-fields --activate");
  } elseif (WP_CLI::runcommand("plugin is-active advanced-custom-fields", ['return' => 'return_code', 'exit_error' => false]) != 0) {
    WP_CLI::runcommand("plugin activate advanced-custom-fields");
  }

  // Remove categories from posts, when category has child categories
  $childless_category_ids = get_categories([
    'childless' => true,
    'hide_empty' => false,
    'fields' => 'ids'
  ]);
  $all_category_ids = get_categories([
    'hide_empty' => false,
    'fields' => 'ids'
  ]);
  $parent_category_ids = array_values(array_diff(
    $all_category_ids,
    $childless_category_ids
  ));

  $post_ids_to_edit = get_posts([
    'category__in' => $parent_category_ids,
    'fields' => 'ids',
    'posts_per_page' => -1,
    'post_status' => 'any'
  ]);

  foreach ($post_ids_to_edit as $post_id) {
    wp_remove_object_terms($post_id, $parent_category_ids, 'category');
  }

  update_option($version_option_name, $new_version);
}

echo 'Deployment complete', PHP_EOL;
