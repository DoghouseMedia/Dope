<?php

/**
 * Dope CLI
 * 
 * 
 * >>> USAGE <<<
 * 
 * $ php path/to/dope/cli.php /controller/action
 * 
 * where /controller/action is the web URL you want to retrieve/execute
 * 
 * 
 * >>> HTTP METHODS <<<
 * 
 * Currently the CLI app can only simulate a GET request.
 * 
 * 
 * >>> ENV vars <<<
 * 
 * You probably need to configure some ENV vars for this to work correctly!
 * 
 * - APPLICATION_ENV
 * Should match the environment tokens from your config file.
 * If not set, this will default to 'production'.
 * Use `$ export APPLICATION_ENV="whatever"` to set it for CLI
 * 
 * 
 * >>> FURTHER NOTES <<<
 * 
 * @todo For now, we're tricking the web app into thinking
 * it's responding to a web request (sneaky right?). At some point,
 * we should probably simplify the bootstrap process and call something
 * like `new MyApp()`
 */

/*
 * Parse request
 * 
 * Set the URI from the first CLI param
 * This is a bit of a hack but so simple that
 * anything else seems like a waste of time. 
 */
if (! empty($_SERVER['argv'][1])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['argv'][1];
}

/*
 * Change the working folder.
 * 
 * Yep, we move into the docroot folder
 * so that the next included file doesn't
 * get too confused.
 */
chdir(dirname(__FILE__) . '/public');

/*
 * Run the app
 * 
 * Simply include the index.php file to trick
 * the app in thinking it's running from a web interface.
 */
require('index.php');
