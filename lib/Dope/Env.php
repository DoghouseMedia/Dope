<?php

namespace Dope;

class Env
{
    const ENV_PRODUCTION = 'production';
    const ENV_STAGING = 'staging';
    const ENV_DEVELOPMENT = 'development';
    
    public static function getEnv()
    {
        return defined('APPLICATION_ENV') ? APPLICATION_ENV : static::ENV_DEVELOPMENT;
    }
    
    public static function isDebug()
	{
		/* We're in debug mode if production mode is NOT on */
		return (bool) (! static::isProduction());
	}
	
	public static function isProduction()
	{
		return (bool) (static::getEnv() == static::ENV_PRODUCTION);
	}
}