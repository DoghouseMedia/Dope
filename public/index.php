<?php

/* Define application environment */
defined('APPLICATION_ENV') || define('APPLICATION_ENV',
	getenv('APPLICATION_ENV') ?: 'production'
);

/* Define path to application directory */
defined('APPLICATION_PATH') || define('APPLICATION_PATH', 
	realpath(dirname(__FILE__) . '/../application')
);

/* Load classes */
require_once APPLICATION_PATH . '/../library/Zend/Application.php';
require_once APPLICATION_PATH . '/../library/Zend/Config/Ini.php';
require_once APPLICATION_PATH . '/../library/Dope/Config/Helper.php';
require_once APPLICATION_PATH . '/../library/Dope/Application.php';

/* Create application */
$application = new \Dope\Application(
    APPLICATION_ENV,
    new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV)
);

/* Bootstrap, and run */
$application
	->bootstrap()
	->run();