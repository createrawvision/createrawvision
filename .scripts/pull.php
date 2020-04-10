<?php

/**
 * This script pulls the database from the given host and updates it to the current local URL.
 * Then it deactivates plugins and edits given options.
 * It also syncs all specified folders and pull other files from GitHub
 * 
 * Requirements:
 * - Valid SSH Key for the server
 * - WP-CLI installed on the server
 * - WP-CLI installed locally
 * - Execute it within your WordPress root directory
 * - Git remote and valid SSH key for it
 * 
 * Usage (from within WP-root):
 * wp eval-file ../.scripts/pull.php [skip-db, skip-files]
 */


/*
 * SETTINGS
 */
$host = 'createrawvision.de';
$user = 'u1138-epznjctshp29';
$port = 18765;
$path = '/home/customer/www/createrawvision.de/public_html';

$old_url = 'https://createrawvision.de';
$new_url = 'http://crv.test';

// folders inside wp-content (without trailing slash!)
$folders = ['mu-plugins', 'plugins', 'uploads/2020'];

$plugins_to_deactivate = array(
    'sg-cachepress',
    'autoptimize'
);


/*
 * OPTIONS
 */
if (!defined('WP_CLI')) {
    echo 'WP_CLI not defined';
    exit(1);
}

$skip_db = in_array('skip-db', $args);
$skip_files = in_array('skip-files', $args);


/*
 * PULLING DATABASE
 */
if ($skip_db) {
    echo 'Skipping database', PHP_EOL;
} else {
    echo 'Pulling Database', PHP_EOL;

    echo 'Testing SSH connection', PHP_EOL;
    if (!valid_ssh($host, $user, $port, $path)) {
        echo 'SHH connection failed', PHP_EOL;
        exit(1);
    }

    echo 'Pulling DB', PHP_EOL;
    if (!pull_db($host, $user, $port, $path)) {
        echo 'Pulling DB failed', PHP_EOL;
        exit(1);
    }

    echo 'Replacing URLs', PHP_EOL;
    if (!replace_url($old_url, $new_url)) {
        echo 'Replacing URLs failed', PHP_EOL;
        exit(1);
    }

    echo 'Deactivating plugins', PHP_EOL;
    deactivate_plugins($plugins_to_deactivate);

    echo 'Deactivating jetpack modules', PHP_EOL;
    if (!remove_option('jetpack_active_modules', ['photon', 'tiled-gallery'])) {
        echo 'Deactivating jetpack modules failed', PHP_EOL;
        exit(1);
    }
}


/*
 * SYNCING FILES
 */
if ($skip_files) {
    echo 'Skipping syncing files', PHP_EOL;
} else {
    echo 'Syncing files', PHP_EOL;

    echo 'Checking directories for wp-content', PHP_EOL;
    if (!wp_content_in_local_path()) {
        echo 'The local path does not have wp-content as a directory', PHP_EOL;
        exit(1);
    }
    if (!wp_content_in_remote_path($host, $user, $port, $path)) {
        echo 'The remote path does not have wp-content as a directory', PHP_EOL;
        exit(1);
    }

    foreach ($folders as $folder) {
        echo "Pulling $folder", PHP_EOL;
        if (!rsync($host, $user, $port, $path, $folder)) {
            echo 'Pulling uploads failed', PHP_EOL;
            exit(1);
        }
    }

    echo 'Pulling files from GitHub', PHP_EOL;
    if (!git_pull()) {
        echo 'Pulling from GitHub failed', PHP_EOL;
        exit(1);
    }
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

function wp_content_in_local_path()
{
    $command = "find . -maxdepth 1 -type d -name 'wp-content'";
    exec($command, $output, $return_var);
    return $return_var == 0 && $output;
}

function  wp_content_in_remote_path($host, $user, $port, $path)
{
    $command = "ssh -p $port $user@$host \"cd $path; find . -maxdepth 1 -type d -name 'wp-content';\"";
    exec($command, $output, $return_var);
    return $return_var == 0 && $output;
}

function rsync($host, $user, $port, $path, $folder)
{
    $command = "rsync -avh --progress --size-only -e 'ssh -p $port' $user@$host:$path/wp-content/$folder/ ./wp-content/$folder";
    system($command, $return_var);
    return $return_var == 0;
}

function git_pull()
{
    system("git pull", $return_var);
    return $return_var == 0;
}

function pull_db($host, $user, $port, $path)
{
    $command = "ssh -p $port $user@$host \"cd $path; wp db export -\" | wp db import -";
    system($command, $return_var);
    return $return_var == 0;
}

function replace_url($old_url, $new_url)
{
    $command = "wp search-replace '$old_url' '$new_url' --recurse-objects --skip-columns=guid";
    system($command, $return_var);
    return $return_var == 0;
}

function remove_option($option_name, $keys_to_remove)
{
    $option_value = get_option($option_name);
    if ($option_value == false) return false;
    if (array_intersect($option_value, $keys_to_remove)) {
        $new_option_value = array_values(array_diff($option_value, $keys_to_remove));
        update_option($option_name, $new_option_value);
    }
    return true;
}
