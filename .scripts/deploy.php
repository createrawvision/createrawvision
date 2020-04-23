<?php

/**
 * Makes changes to the database by comparing 'crv_version' option.
 * 
 * Execute with WP-CLI!
 */

if (!defined('WP_CLI') || !WP_CLI) {
  echo "WP_CLI not defined", PHP_EOL;
  exit(1);
}

/**
 * Run a WP_CLI command.
 * 
 * Run `wp cli has_command` before executing the command.   
 * Only exits, if the command couldn't be executed and `exit_error` is set to exit.
 */
function run_wp_cli_command($command, $exit_error = false)
{
  if ($exit_error == 'exit_error') {
    $exit_error = true;
  }
  if (!is_bool($exit_error)) {
    throw new InvalidArgumentException("exit_error has to be one of true, false or 'exit_error': was $exit_error");
  }

  $options = array(
    'launch'      => false,
    'exit_error'  => $exit_error,
    'return'      => 'return_code'
  );

  if (wp_cli_has_command($command)) {
    return WP_CLI::runcommand($command, $options);
  } else {
    $message = "Couldn't find command \"${command}\"";
    if ($exit_error) {
      WP_CLI::error($message);
    } else {
      WP_CLI::warning($message);
      return 1;
    }
  }
}

/**
 * Checks if WP-CLI knows the command.
 * 
 * Escapes quotes by running `addslahes` on the input.
 */
function wp_cli_has_command($command)
{
  $command = addslashes($command);
  $return_code = WP_CLI::runcommand("cli has-command \"${command}\"", array(
    'return'      => 'return_code',
    'exit_error'  => false
  ));
  return $return_code == 0;
}

// Avoid the output buffer
ob_end_flush();
ob_implicit_flush();

// Check the version to deploy all needed changes
$version_option_name = 'crv_version';
$version = get_option($version_option_name);

if (!$version) {
  $version = '0.0.0';
  add_option($version_option_name, $version);
}

$new_version = '0.1.0';
if (version_compare($version, $new_version, '<')) {
  WP_CLI::log("Deploying version $new_version");

  /**
   * Setup for category featured images
   */
  WP_CLI::log("Installing and activating Advanced Custom Fields");
  run_wp_cli_command("plugin install advanced-custom-fields --activate --force", 'exit_error');

  WP_CLI::log("Removing categories from posts, when category has child categories");

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
  WP_CLI::log("Removed parent categories from following posts: " . implode(',', $post_ids_to_edit));


  /**
   * Restrict Content Pro setup
   * 
   * @todo Get the final 'rcp_settings' option (with payment gateway!)
   * 
   * @todo Insert mebership level
   * 
   * @todo Restrict site content (a2kA1_termmeta -> meta_key = rcp_restricted_meta)
   */
  WP_CLI::log("Installing and activating Restrict Content Pro");
  $rcp_path = ABSPATH . '../deployment_data/restrict-content-pro.zip';
  run_wp_cli_command("plugin install '$rcp_path' --activate --force", 'exit_error');

  WP_CLI::warning("RCP license key wasn't set. Add the license key manually.");


  /**
   * Setup for support/faq page
   * 
   * @todo create page with slug 'faqs'
   * 
   * @todo import all faqs with custom category
   */


  /**
   * Setup for member dashboard
   * 
   * @todo create page with slug 'dashboard'
   */

  update_option($version_option_name, $new_version);
  WP_CLI::success("Deployed version " . $new_version);
}

WP_CLI::log("Flushing all caches");
run_wp_cli_command('sg purge');
run_wp_cli_command("autoptimize clear");
run_wp_cli_command("cache flush");
run_wp_cli_command("rewrite flush");

WP_CLI::success('Deployment complete');
