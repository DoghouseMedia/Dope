<?php

namespace Dope\Controller\Action\Helper;

use Dope\Auth\Service as AuthService,
	Dope\Config\Helper as Config,
	Zend_Controller_Action_HelperBroker as HelperBroker;

class Acl extends \Zend_Controller_Action_Helper_Abstract
{
	/**
	 * @var \Dope\Auth\Acl
	 */
	protected $acl;

	/**
	 * Constructor
	 * 
	 * @param  \Dope\Auth\Acl $acl
	 * @return void
	 */
	public function __construct(\Dope\Auth\Acl $acl=null)
	{
		$this->acl = $acl ?: \Dope\Auth\Service::getAcl();
	}
	
	public function getAcl()
	{
		return $this->acl;
	}

	/**
	 * Hook into action controller preDispatch() workflow
	 *
	 * @return void
	 */
	public function preDispatch()
	{
		$controller = $this->getActionController();
		$controllerName = $controller->getRequest()->getControllerName();
		
		if(! $this->getAcl()->has($controllerName)) {
			$this->getAcl()->addResource(new \Zend_Acl_Resource($controllerName));
		}
		
		/**
		 * @todo A lot of stuff here belongs to the model, and not the library !!
		 */
		
		$resource = $this->_generateResource($controller->getRequest());
		$privilege = $controller->getRequest()->getActionName();

		if (! $this->isAllowed($resource, $privilege)) {
			HelperBroker::getStaticHelper('redirector')->gotoUrl(
				AuthService::hasUser()
					? Config::getOption('url.app.error.denied')
					: Config::getOption('url.app.auth.login')
			);
		}
	}
	
	/**
	 * In order to handle advanced dynamic ACL functions, we can send an object through to \Zend_Acl
	 * @deprecated Use \Core_Acl instead
	 */
	protected function _generateResource($request)
	{	
		$controller = $request->getControllerName();
		$id = (int) $request->getParam('id');
		$classname = ucfirst($controller);
		
		if(in_array($classname, get_declared_classes()) && $id > 0) { 
			/*
			 * This is a request for an actual resource.
			 * We need to instance it and pass it on to Zend_Acl so it
			 * can pass it on to our Acl Asserts.
			 */
	   		$resource = new $classname($id);
		} else {
			$resource = $controller;	
		}
		
		return $resource;
	}

	public function isAllowed($resource=null, $privilege=null)
    {
    	return AuthService::isAllowed(
        	$resource,
        	$privilege
        );
    }
}
