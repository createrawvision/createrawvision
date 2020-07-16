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
 * wp eval-file .scripts/pull.php [skip-db, skip-files, local]
 */


/*
 * SETTINGS
 */
$host = 'createrawvision.de';
$user = 'u1138-epznjctshp29';
$port = 18765;
$path = '/home/customer/www/createrawvision.de/public_html';

$old_url = 'https://createrawvision.de';

// folders inside wp-content (without trailing slash!)
$folders = array( 'mu-plugins', 'plugins', 'uploads/2020' );

$plugins_to_deactivate = array(
	'sg-cachepress',
	'autoptimize',
);


/*
 * OPTIONS
 */
if ( ! defined( 'WP_CLI' ) ) {
	echo 'WP_CLI not defined';
	exit( 1 );
}

$skip_db    = in_array( 'skip-db', $args );
$skip_files = in_array( 'skip-files', $args );
$local      = in_array( 'local', $args );


// Avoid the output buffer
ob_end_flush();
ob_implicit_flush();

if ( $local ) {
	$pull_helper = new LocalPullHelper( $path, $old_url, get_bloginfo( 'url' ) );
} else {
	$pull_helper = new RemotePullHelper( $host, $user, $port, $path, $old_url, get_bloginfo( 'url' ) );
}

WP_CLI::log( 'Asserting conditions to run script' );
if ( ! $pull_helper->assert_conditions() ) {
	WP_CLI::error( 'Couldn\'t assert all conditions' );
}

/*
 * PULLING DATABASE
 */
if ( $skip_db ) {
	WP_CLI::log( 'Skipping database' );
} else {
	WP_CLI::log( 'Pulling Database' );

	WP_CLI::log( 'Importing Database' );
	if ( ! $pull_helper->pull_db() ) {
		WP_CLI::error( 'Pulling DB failed' );
	}

	WP_CLI::log( 'Replacing URLs' );
	if ( ! $pull_helper->replace_url() ) {
		WP_CLI::error( 'Replacing URLs failed' );
	}

	WP_CLI::log( 'Deactivating plugins' );
	deactivate_plugins( $plugins_to_deactivate );

	WP_CLI::log( 'Deactivating jetpack modules' );
	if ( ! $pull_helper->remove_option( 'jetpack_active_modules', array( 'photon', 'tiled-gallery' ) ) ) {
		WP_CLI::error( 'Deactivating jetpack modules failed' );
	}

	WP_CLI::success( 'Pulling database complete' );
}


/*
 * SYNCING FILES
 */
if ( $skip_files ) {
	WP_CLI::log( 'Skipping syncing files' );
} else {
	WP_CLI::log( 'Syncing files' );

	foreach ( $folders as $folder ) {
		WP_CLI::log( "Pulling $folder" );
		if ( ! $pull_helper->sync_files( $folder ) ) {
			WP_CLI::error( 'Pulling uploads failed' );
		}
	}

	WP_CLI::log( 'Pulling files from GitHub' );
	if ( ! $pull_helper->git_pull() ) {
		WP_CLI::error( 'Pulling from GitHub failed' );
	}

	WP_CLI::log( 'Syncing files complete' );
}


/**
 * Helper Class for pulling files and database
 */
abstract class PullHelper {

	/**
	 * Checks that all conditions to run methods are met
	 */
	abstract public function assert_conditions();

	/**
	 * Checks if wp-content folder is in current directory
	 */
	protected function wp_content_in_path( $path = '.' ) {
		$command = "find '$path' -maxdepth 1 -type d -name 'wp-content'";
		exec( $command, $output, $return_var );
		return $return_var == 0 && $output;
	}

	/**
	 * Gets the files from the source
	 */
	abstract public function sync_files( $folder);

	/**
	 * Pull files from GitHub
	 *
	 * @return boolean TRUE on success
	 */
	public function git_pull() {
		system( 'git pull', $return_var );
		return $return_var == 0;
	}

	/**
	 * Gets the database from the source
	 */
	abstract public function pull_db();

	/**
	 * Replace all URLs in database
	 *
	 * @return boolean TRUE on success
	 */
	public function replace_url() {
		 $command = "wp search-replace '{$this->old_url}' '{$this->new_url}' --recurse-objects --skip-columns=guid";
		system( $command, $return_var );
		return $return_var == 0;
	}

	/**
	 * Removes entries in an option by keys
	 */
	public function remove_option( $option_name, $keys_to_remove ) {
		$option_value = get_option( $option_name );
		if ( $option_value == false ) {
			return false;
		}
		if ( array_intersect( $option_value, $keys_to_remove ) ) {
			$new_option_value = array_values( array_diff( $option_value, $keys_to_remove ) );
			update_option( $option_name, $new_option_value );
		}
		return true;
	}
}

/**
 * Helper Class for pulling from remote source
 */
class RemotePullHelper extends PullHelper {

	public function __construct( $host, $user, $port, $path, $old_url, $new_url ) {
		 $this->host   = $host;
		$this->user    = $user;
		$this->port    = $port;
		$this->path    = $path;
		$this->old_url = $old_url;
		$this->new_url = $new_url;
	}

	public function assert_conditions() {
		return $this->valid_ssh()
			&& $this->wp_content_in_path()
			&& $this->wp_content_in_source_path();
	}

	private function valid_ssh() {
		$command = "ssh -q -p {$this->port} {$this->user}@{$this->host} \"cd {$this->path}; exit;\"";
		system( $command, $return_var );
		return $return_var == 0;
	}

	private function wp_content_in_source_path() {
		$command = "ssh -p {$this->port} {$this->user}@{$this->host} \"cd {$this->path}; find . -maxdepth 1 -type d -name 'wp-content';\"";
		exec( $command, $output, $return_var );
		return $return_var == 0 && $output;
	}

	public function sync_files( $folder ) {
		 $command = "rsync -avh --progress -e 'ssh -p {$this->port}' {$this->user}@{$this->host}:{$this->path}/wp-content/$folder/ ./wp-content/$folder";
		system( $command, $return_var );
		return $return_var == 0;
	}

	public function pull_db() {
		 $command = "ssh -p {$this->port} {$this->user}@{$this->host} \"cd {$this->path}; wp db export -\" | wp db import -";
		system( $command, $return_var );
		return $return_var == 0;
	}
}


/**
 * Helper class for pulling from local source
 */
class LocalPullHelper extends PullHelper {

	public function __construct( $path, $old_url, $new_url ) {
		$this->path    = $path;
		$this->old_url = $old_url;
		$this->new_url = $new_url;
	}

	public function assert_conditions() {
		return $this->wp_content_in_path()
			&& $this->wp_content_in_path( $this->path );
	}

	public function sync_files( $folder ) {
		 $command = "rsync -avh --progress {$this->path}/wp-content/$folder/ ./wp-content/$folder";
		system( $command, $return_var );
		return $return_var == 0;
	}

	public function pull_db() {
		 $command = "wp db export --path={$this->path} - | wp db import -";
		system( $command, $return_var );
		return $return_var == 0;
	}
}
