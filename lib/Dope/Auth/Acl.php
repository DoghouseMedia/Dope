<?php

namespace Dope\Auth;

use \Zend_Acl_Resource as Resource,
	\Zend_Acl_Role as Role;

class Acl extends \Zend_Acl
{
	public function __construct(\Zend_Auth $auth = null)
	{
		/* 
		 * Resources
		 * 
		 * We only need to define the ones we use below,
		 * since the other ones are defined on the fly
		 */
		$this->add(new Resource('auth'));
		$this->add(new Resource('user'));
		$this->add(new Resource('error'));	
		$this->add(new Resource('sync'));
		$this->add(new Resource('tab'));
		$this->add(new Resource('file'));
		$this->add(new Resource('data'));
		$this->add(new Resource('dev'));
		$this->add(new Resource('report'));
		$this->add(new Resource('placement'));

		/* Roles */
		$this->addRole(new Role('guest')); 
		$this->addRole(new Role('consultant'));
		$this->addRole(new Role('admin'));
		$this->addRole(new Role('dev'));
		$this->addRole(new Role('superuser'));

		/* Guest may only log in */
		$this->deny('guest');
		$this->allow('guest', 'auth');
		$this->allow('guest', 'error');
		
		/* Consultants */
		$this->allow('consultant');
		$this->deny('consultant', 'user', 'impersonate');
		$this->deny('consultant', null, 'delete');
		$this->allow('consultant', 'tab', 'delete'); // allow consultants to delete tabs
		$this->allow('consultant', 'file', 'delete'); // allow consultants to delete files
		$this->deny('consultant', 'placement', 'terminate'); // deny placement terminations
		$this->deny('consultant', 'placement', 'amend'); // deny placement amendments
		
		/* Reports */
		$this->deny('consultant', 'report');
		$this->allow('consultant', 'report', 'list');
		$this->allow('consultant', 'report', 'candidatecategory');
		$this->allow('consultant', 'report', 'clientcomment');
		$this->allow('consultant', 'report', 'clientvisit');
		$this->allow('consultant', 'report', 'comment');
		$this->allow('consultant', 'report', 'currentjob');
		$this->allow('consultant', 'report', 'cvsent');
		$this->allow('consultant', 'report', 'reminder');
		$this->allow('consultant', 'report', 'lastcommentbycategory');
		$this->allow('consultant', 'report', 'lastcontact');
		
		/* Role: admin */
		$this->allow('admin'); // unrestricted access
		$this->deny('admin', 'dev'); // except dev
		
		/* Role: dev */
		$this->allow('dev'); // unrestricted access
		
		/* Role: superuser */
		$this->allow('superuser'); // unrestricted access

		/* CLI */
		if (php_sapi_name() == 'cli') {
			/* Allow "guest" to access ALL if running from CLI */
			$this->allow('guest'); // unrestricted access
		}
	}
}

