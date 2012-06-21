<?php

/**
 * CLI settings
 * 
 * You probably need to configure some ENV vars for this to work correctly! 
 * 
 * APPLICATION_ENV
 * 
 * If not set, this will default to 'production'.
 * Example: "staging"
 * 
 * APPLICATION_ZF_PATH
 * 
 * Unless your php-cli.ini file is setup with ZF in it's default include_path, you will to configure this correctly.
 * Example: "/usr/local/zend/share/ZendFramework/library"
 * 
 */

if (! empty($_SERVER['argv'][1])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['argv'][1];
}

require('../public/index.php');
