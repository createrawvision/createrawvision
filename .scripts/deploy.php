<?php

/**
 * This script deploys the changes from GitHub to the given server.
 * NOTE: Only the database gets backed up! Backup your files manually, if you want to.
 * 
 * Requirements:
 * - Valid SSH Key for the server
 * - WP-CLI installed on the server
 * - WP-CLI installed locally (made required, so the script can only be run through WP-CLI)
 * - SSH path has to be the WordPress root directory
 * - Git remote and valid SSH key for it on the server
 * 
 * Usage:
 * wp eval-file ../.scripts/deploy.php
 */

/*
 * SETTINGS
 */
$host = 'createrawvision.de';
$user = 'u1138-epznjctshp29';
$port = 18765;
$path = '/home/customer/www/staging3.createrawvision.de/public_html';
$path_to_script = $path . '/wp-content/deploy.php';


/*
 * CHECKS
 */
if (!defined('WP_CLI')) {
  echo 'WP_CLI not defined';
  exit(1);
}

echo 'Testing SSH connection', PHP_EOL;
if (!valid_ssh($host, $user, $port, $path)) {
  echo 'SHH connection failed', PHP_EOL;
  exit(1);
}

echo 'Checking if WP CLI is on the server', PHP_EOL;
if (!is_remote_wp_cli($host, $user, $port, $path)) {
  echo 'WP CLI not on server', PHP_EOL;
  exit(1);
}


/*
 * COMMAND
 */
echo 'Building remote command', PHP_EOL;

$deploy_command = "echo 'Activating maintenance mode';";
$deploy_command .= "wp maintenance-mode activate;";

$deploy_command .= "echo 'Creating Database Backup';";
$deploy_command .= "wp db export;";

$deploy_command .= "echo 'Pulling from GitHub';";
$deploy_command .= "git pull;";

$deploy_command .= "echo 'Running deployment script';";
$deploy_command .= "if [ -f '$path_to_script' ]; then wp eval-file '$path_to_script'; else echo 'Script not found'; fi;";

$deploy_command .= "echo 'Deactivating maintenance mode';";
$deploy_command .= "wp maintenance-mode deactivate;";

echo 'Launching remote command via ssh', PHP_EOL;
echo $deploy_command;
if (!execute_remote($deploy_command, $host, $user, $port, $path)) {
  echo 'Remote command failed', PHP_EOL;
  exit(1);
}


/*
 * FUNCTIONS
 */

function valid_ssh($host, $user, $port, $path)
{
  $command = "ssh -q -p $port $user@$host \"cd $path; exit;\"";
  system($command, $return_var);
  return $return_var == 0;
}

function is_remote_wp_cli($host, $user, $port, $path)
{
  $command = "ssh -q -p $port $user@$host \"cd $path; wp cli version;\"";
  system($command, $return_var);
  return $return_var == 0;
}

function execute_remote($command, $host, $user, $port, $path)
{
  $command = "ssh -p $port $user@$host \"cd $path; $command\"";
  system($command, $return_var);
  return $return_var == 0;
}
