<?php

namespace Dope\Auth;
use 
	Dope\Doctrine\Auth\Adapter,
	Dope\Auth\Acl,
	Dope\Entity\User,
	Dope\Config\Helper as Config,
	Zend_Auth as Auth,
	Zend_Session as Session;

class Service
{
	protected static $user;
	protected static $acl;
	protected static $loaded = false;
	
	public static function getAclClass()
	{
	    return Config::getOption('appnamespace') . '\Auth\Acl';
	}
	
	public static function getUserEntityClass()
	{
	    return Config::getOption('appnamespace') . '\Entity\User';
	}
	
	public static function getUserRepository()
	{
	    return \Dope\Doctrine::getEntityManager()->getRepository(
            static::getUserEntityClass()
	    );
	}
	
        public static function authenticate($username, $password)
        {
            $authAdapter = new Adapter();
            $em = \Dope\Doctrine::getEntityManager();

            $password = call_user_func(
                array(
                    static::getUserEntityClass(),
                    'encryptPassword',
                ),
                $password
            );
		
            $authAdapter
                ->setEntityManager($em)
                ->setTableClass(static::getUserEntityClass())
                ->setIdentityColumn('username')
                ->setCredentialColumn('password')
                ->setIdentity($username)
                ->setCredential($password);

            $result = Auth::getInstance()->authenticate($authAdapter);

            if (! $result->isValid()) {
                return false;
            }

            $user = static::getUserRepository()->findOneBy(array(
                'username' => $username
            ));

            static::setUser($user);
            return true;
        }
	
	public static function reset()
	{
		Auth::getInstance()->clearIdentity();
		unset($_SESSION);
		return true;
	}
	
	public static function setUser(User $user)
	{
		Auth::getInstance()
			->getStorage()
			->write($user->getUsername());
		
		static::$user = $user;
		
		return true;
	}
	
	public static function hasUser()
	{
		static::loadUser();
		return (bool) (Auth::getInstance()->getIdentity());
	}
	
	/**
	 * Get singleton instance of the user
	 * 
	 * @return \Dope\Entity\User
	 */
	public static function getUser()
	{
		if (! static::hasUser()) {
			return false;
		}
		
		if (! static::$user) {
			static::$user = static::getUserRepository()->findOneBy(array(
			    'username' => Auth::getInstance()->getIdentity()
			));
		}
		
		return static::$user;
	}
	
	protected static function loadUser()
	{
		if (! static::$loaded) {
			Auth::getInstance();
			static::$loaded = true;
		}
	}
	
	protected static function release()
	{
		if (Session::isWritable()) {
			Session::writeClose(true);
		}
	}
	
	public static function getAcl()
	{
		if (! static::$acl instanceof Acl) {
		    $aclClass = static::getAclClass();
			static::$acl = new $aclClass();
		}
		
		return static::$acl;
	}
	
	public static function isAllowed($resource=null, $privilege=null)
	{
		/* Check for guest access to avoid using session */
		if (static::getAcl()->isAllowed('guest', $resource, $privilege)) {
			return true;
		}
		
		/* Load and release authinfo (probably session lock) */
		static::loadUser();
		static::release();
		
		/* Ask auth service */
		if (static::hasUser()) {
			return static::getAcl()->isAllowed(
				static::getUser()->role,
				$resource,
				$privilege
			);
		}
		else {
			return false;
		}
	}
}
