<?php

namespace Dope\Auth;

use \Zend_Acl_Resource as Resource,
	\Zend_Acl_Role as Role;

abstract class Acl extends \Zend_Acl
{
	abstract public function __construct(\Zend_Auth $auth = null);
}

