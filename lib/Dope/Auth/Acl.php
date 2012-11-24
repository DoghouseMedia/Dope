<?php

namespace Dope\Auth;

use \Zend_Acl_Resource as Resource,
	\Zend_Acl_Role as Role;

class Acl extends \Zend_Acl
{
	public function __construct(\Zend_Auth $auth = null)
	{
		parent::__construct($auth);
	}
}

