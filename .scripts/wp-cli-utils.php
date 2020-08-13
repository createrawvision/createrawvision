<?php

/**
 * Run a WP_CLI command.
 *
 * Run `wp cli has_command` before executing the command.
 * Only exits, if the command couldn't be executed and `exit_error` is set to exit.
 * `$options['return']` can be `'return_code'`, `'stdout'`, `'stderr'` or `'all'`
 */
function run_wp_cli_command( $command, $options = array() ) {
	$default_options = array(
		'launch'     => false,
		'exit_error' => false,
		'return'     => 'return_code',
	);

	$options = array_merge( $default_options, $options );

	if ( wp_cli_has_command( $command ) ) {
		return WP_CLI::runcommand( $command, $options );
	} else {
		$message = "Couldn't find command \"${command}\"";
		if ( $options['exit_error'] ) {
			WP_CLI::error( $message );
		} else {
			WP_CLI::warning( $message );
		}
	}
}

/**
 * Checks if WP-CLI knows the command.
 *
 * Escapes quotes by running `addslahes` on the input.
 */
function wp_cli_has_command( $command ) {
	$command     = addslashes( $command );
	$return_code = WP_CLI::runcommand(
		"cli has-command \"${command}\"",
		array(
			'launch'     => false,
			'return'     => 'return_code',
			'exit_error' => false,
		)
	);
	return $return_code == 0;
}
