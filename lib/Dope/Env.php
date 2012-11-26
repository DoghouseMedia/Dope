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
        /*
         * Because dope doesn't yet support the new dojo, and isDebug
         * is used in several places to switch between a local and CDN
         * version of dojo, for now we just force isDebug to true
         * @todo: Fix support for latest dojo
         */
        return true;

        /* We're in debug mode if production mode is NOT on */
        return (bool) (! static::isProduction());
    }
	
    public static function isProduction()
    {
        return (bool) (static::getEnv() == static::ENV_PRODUCTION);
    }
}
