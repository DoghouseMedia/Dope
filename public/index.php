<?php

error_reporting(E_ALL ^ E_STRICT);

/* Define application environment */
defined('APPLICATION_ENV') || define('APPLICATION_ENV',
	getenv('APPLICATION_ENV') ?: 'production'
);

/* Define path to application directory */
defined('APPLICATION_PATH') || define('APPLICATION_PATH',
	realpath(dirname(__FILE__) . '/../../application')
);

set_include_path('../lib');

/* Load classes */
require_once '../lib/Zend/Application.php';
require_once '../lib/Zend/Config/Ini.php';
require_once '../lib/Dope/Config/Helper.php';
require_once '../lib/Dope/Application.php';

/* Create application */
$application = new \Dope\Application(
    APPLICATION_ENV,
    new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV)
);

/* Bootstrap, and run */
$application
	->bootstrap()
	->run();
