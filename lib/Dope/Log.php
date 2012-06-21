<?php

namespace Dope;

class Log
{
	protected static $writer;
	protected static $logger;
	
	/**
	 * @deprecated Use console() instead
	 * @param mixed $message
	 */
	public static function firebug($message)
	{
		return static::console($message);
	}
	
	/**
	 * @param mixed $message
	 */
	public static function console($message)
	{
		if (! static::$writer OR ! static::$logger) {
			static::$writer = new \Zend_Log_Writer_Firebug();
			static::$logger = new \Zend_Log(static::$writer);
		}

		return static::$logger->log($message, \Zend_Log::WARN);
	}
	
	/**
	 * @param mixed $message
	 */
	public static function profile($message)
	{
		return static::console('[' . date('Y-m-d H:i:s') . '] ' . $message);
	}
}