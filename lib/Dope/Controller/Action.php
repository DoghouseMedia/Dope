<?php

namespace Dope\Controller;

abstract class Action
extends \Zend_Controller_Action
{
	public function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
	{
		/* View helper */
		\Zend_Controller_Action_HelperBroker::addHelper(new Action\Helper\View());
		
		/* Call parent constructor */
		parent::__construct($request, $response, $invokeArgs);
	}
	
	public function init()
	{
		parent::init();
		
		/* Context Switcher */
		$this->_helper->contextSwitch->initContext();
	}
	
	public function __call($methodName, $args)
	{
		/*
		 * If method XXXAction is called and doesn't exist,
		 * this allows us to render XXX.html.phtml as view
		 * without defining action.
		 */
		if (substr($methodName, -6) == 'Action') {
			$action = substr($methodName, 0, -6);
			$this->_helper->contextSwitch
				->addActionContext($action, array('html'))
				->initContext();
	
			return; // stop parent
		}
	
		return parent::__call($methodName, $args);
	}
	
	/* ---------------| HELPER methods from here on |--------------- */
	
	/**
	 * Pass data through our Data class for filtering, etc...
	 *
	 * @param array $data
	 * @return \Dope\Controller\Data
	 */
	public function getData(array $data=null)
	{
		return new Data(
			is_array($data) ? $data : $this->getRequest()->getParams()
		);
	}
	
	public function getFormUniqueId()
	{
		$view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		return preg_replace('/[^A-z0-9_]/', '_',
			$view->uniqueId('form')
		);
	}
	
	/**
	 * Override getParam method to allow for $tryData
	 * $tryData is an extra array that is searched before getting values from Request
	 */
	protected function _getParam($key, $defaultValue=null, array $tryData=null)
	{
		if (is_array($tryData) AND array_key_exists($key, $tryData)) {
			return $tryData[$key];
		}
	
		return parent::_getParam($key, $defaultValue);
	}
	
	/**
	 * @deprecated Use \Dope\Log instead
	 * @param mixed $message
	 */
	protected function log($message)
	{
		//
	}
}