<?php

namespace Dope\Config;

class Helper
{
	protected static $config;
	
	public static function setConfig(\Zend_Config $config)
	{
		static::$config = $config;
	}
	
	/**
	 * @return \Zend_Config
	 */
	public static function getConfig()
	{
		return static::$config;
	}
	
	public static function getOption($name)
	{
		$return = static::getConfig();
	
		foreach(explode('.', $name) as $key) {
			if (! isset($return->$key)) {
				return null;
			}
			
			$return = $return->$key;
		}

		return $return;
	}
}