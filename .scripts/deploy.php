<?php

/**
 * Makes changes to the database by comparing 'crv_version' option.
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

// Avoid the output buffer
ob_end_flush();
ob_implicit_flush();

// Check the version to deploy all needed changes
$version_option_name = 'crv_version';
$current_version = get_option($version_option_name);

if (!$current_version) {
  $current_version = '0.0.0';
  add_option($version_option_name, $current_version);
}

$all_versions = [
  '0.1.0'
];
usort(array_unique($all_versions), 'version_compare');

foreach ($all_versions as $new_version) {
  if (version_compare($current_version, $new_version, '<')) {
    WP_CLI::log("Deploying version $new_version");

    require __DIR__ . "/versions/deploy-${new_version}.php";

    // Update verion in database
    update_option($version_option_name, $new_version);
    WP_CLI::success("Deployed version " . $new_version);
  }
}


WP_CLI::log("Flushing all caches");
run_wp_cli_command('sg purge');
run_wp_cli_command("autoptimize clear");
run_wp_cli_command("cache flush");
run_wp_cli_command("rewrite flush");

WP_CLI::success('Deployment complete');
