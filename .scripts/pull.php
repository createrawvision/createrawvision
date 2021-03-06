<?php
/**
 * This script pulls the database from the given host and updates it to the current local URL.
 * It also syncs all specified folders and pull other files from GitHub
 *
 * Requirements:
 * - Valid SSH Key for the server
 * - WP-CLI installed on the server
 * - WP-CLI installed locally
 * - Git remote and valid SSH key for it
 *
 * Usage:
 * wp eval-file .scripts/pull.php [skip-db, skip-files, local, deactivate-plugins]
 */

/*
 * SETTINGS
 */
$host        = 'createrawvision.de';
$user        = 'u1138-epznjctshp29';
$port        = 18765;
$source_path = '/home/customer/www/createrawvision.de/public_html';

$old_url = 'https://createrawvision.de';

// Folders inside wp-content, without trailing slash!
$folders = array( 'languages', 'mu-plugins', 'plugins', 'uploads/2020' );

$plugins_to_deactivate = array( 'antispam-bee', 'google-analytics-for-wordpress', 'sg-cachepress', 'wordfence' );

/*
 * OPTIONS
 */
if ( ! defined( 'WP_CLI' ) ) {
	echo 'WP_CLI not defined';
	exit( 1 );
}
$skip_db            = in_array( 'skip-db', $args, true );
$skip_files         = in_array( 'skip-files', $args, true );
$local              = in_array( 'local', $args, true );
$deactivate_plugins = in_array( 'deactivate-plugins', $args, true );

// Avoid the output buffer.
ob_end_flush();
ob_implicit_flush();

if ( $local ) {
	$pull_helper = new LocalPullHelper( $source_path, $old_url, get_bloginfo( 'url' ) );
} else {
	$pull_helper = new RemotePullHelper( $host, $user, $port, $source_path, $old_url, get_bloginfo( 'url' ) );
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
			WP_CLI::error( "Pulling $folder failed" );
		}
	}

	WP_CLI::log( 'Pulling files from GitHub' );
	if ( ! $pull_helper->git_pull() ) {
		WP_CLI::error( 'Pulling from GitHub failed' );
	}

	WP_CLI::log( 'Syncing files complete' );
}

/*
 * DEACTIVATING PLUGINS
 */
if ( $deactivate_plugins ) {
	WP_CLI::log( 'Deactivating plugins: ' . join( ', ', $plugins_to_deactivate ) );

	// Launch a new process, since the currently loaded WordPress instance is overwritten.
	$command = 'wp plugin deactivate ' . join( ' ', $plugins_to_deactivate );
	system( $command, $return_var );
	if ( 0 !== $return_var ) {
		WP_CLI::error( 'Deactivating plugins failed' );
	}

	WP_CLI::log( 'Deactivating plugins complete' );
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
		return 0 === $return_var && $output;
	}

	/**
	 * Gets the files from the source
	 */
	abstract public function sync_files( $folder );

	/**
	 * Pull files from GitHub
	 *
	 * @return boolean TRUE on success
	 */
	public function git_pull() {
		system( 'git pull', $return_var );
		return 0 === $return_var;
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
		return 0 === $return_var;
	}
}

/**
 * Helper Class for pulling from remote source
 */
class RemotePullHelper extends PullHelper {

	public function __construct( $host, $user, $port, $path, $old_url, $new_url ) {
		$this->host    = $host;
		$this->user    = $user;
		$this->port    = $port;
		$this->path    = $path;
		$this->old_url = $old_url;
		$this->new_url = $new_url;
	}

	public function assert_conditions() {
		return $this->valid_ssh()
			&& $this->wp_content_in_source_path();
	}

	private function valid_ssh() {
		$command = "ssh -q -p {$this->port} {$this->user}@{$this->host} \"cd {$this->path}; exit;\"";
		system( $command, $return_var );
		return 0 === $return_var;
	}

	private function wp_content_in_source_path() {
		$command = "ssh -p {$this->port} {$this->user}@{$this->host} \"cd {$this->path}; find . -maxdepth 1 -type d -name 'wp-content';\"";
		exec( $command, $output, $return_var );
		return 0 === $return_var && $output;
	}

	public function sync_files( $folder ) {
		$command = "rsync -avh --progress -e 'ssh -p {$this->port}' {$this->user}@{$this->host}:{$this->path}/wp-content/$folder/ " . ABSPATH . "wp-content/$folder";
		system( $command, $return_var );
		return 0 === $return_var;
	}

	public function pull_db() {
		$command = "ssh -p {$this->port} {$this->user}@{$this->host} \"cd {$this->path}; wp db export -\" | wp db import -";
		system( $command, $return_var );
		return 0 === $return_var;
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
		return $this->wp_content_in_path( $this->path );
	}

	public function sync_files( $folder ) {
		$command = "rsync -avh --progress {$this->path}/wp-content/$folder/ " . ABSPATH . "wp-content/$folder";
		system( $command, $return_var );
		return 0 === $return_var;
	}

	public function pull_db() {
		$command = "wp db export --path={$this->path} - | wp db import -";
		system( $command, $return_var );
		return 0 === $return_var;
	}
}
