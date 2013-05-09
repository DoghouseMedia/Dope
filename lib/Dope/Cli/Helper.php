<?php

namespace Dope\Cli;

class Helper
{
	public static function checkIniSettings()
	{
	    /* Check max_execution_time */
	    if (ini_get('max_execution_time') != '0') {
	        $maxExecTime = 0;
	
	        // Apply limit
	        ini_set('max_execution_time', $maxExecTime);
	
	        // Check limit was applied successfully
	        if ($maxExecTime != ini_get('max_execution_time')) {
	            throw new \Exception("Unable to set max_execution_time to $maxExecTime seconds!");
	        }
	    }
	
	    /* Check memory_limit */
	    if (ini_get('memory_limit') != '-1') {
	        $memoryLimit = '-1'; // no limit
	
	        // Apply limit
	        ini_set('memory_limit', $memoryLimit);
	
	        // Check limit was applied successfully
	        if ($memoryLimit != ini_get('memory_limit')) {
	            throw new \Exception("Unable to set memory_limit to $memoryLimit!");
	        }
	    }
	
	    ini_set('implicit_flush', 'On');
	}
	
	/*
	 * ---------- PID Helpers ----------
	 */
	
	public static function getPidFilePath($name)
	{
		$dir = APPLICATION_PATH . '/../var/pid/';
		
		if (! file_exists($dir)) {
			if (! mkdir($dir, 0777, true)) {
				throw new \Exception("Unable to create folder: $dir");
			}
			@chmod($dir, 0777);
		}
		
	    return $dir . preg_replace('/\W/', '-', $name) . '.pid';
	}
	
	public static function readPidFile($name)
	{
		$filePath = static::getPidFilePath($name);
		
	    return file_exists($filePath) ? file_get_contents($filePath) : false;
	}
	
	public static function writePidFile($name, $pid)
	{
		$filePath = static::getPidFilePath($name);
	    $result = (bool) file_put_contents($filePath, $pid);
	    @chmod($filePath, 0777);
	    
	    return $result;
	}
	
	public static function lockPidFile($name)
	{	
		$lastPid = static::readPidFile($name);
		
		if ($lastPid AND static::isPidRunning($lastPid)) {
			throw new \Exception('PID exists and is running for ' . $name);
		}
		
		return static::writePidFile($name, getmypid());
	}
	
	public static function unlockPidFile($name)
	{
		return static::writePidFile($name, null);
	}
	
	public static function isPidRunning($pid)
	{
		return $pid ? file_exists('/proc/' . (int) $pid) : false;
	}
	
	public static function findPidsByName($name)
	{
		/*
		 * @author Leif Madsen
		 * @see http://leifmadsen.wordpress.com/2011/09/15/return-just-pid-of-script-with-ps-and-awk/
		 */
		$exec = exec('ps -eo pid,command | grep "' . $name . '" | grep -v grep | awk \'{print $1}\'');
		
		return $exec ? explode("\n", $exec) : array();
	}
}