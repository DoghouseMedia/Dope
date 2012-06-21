<?php
namespace Dope;

class Application extends \Zend_Application
{
	public function __construct($environment, $options = null)
	{
		if (! $options instanceof \Zend_Config) {
			throw new \Exception("Options MUST be an instance of Zend_Config!");
		}
		
		Config\Helper::setConfig($options);
		
		parent::__construct($environment, $options);
	}
}