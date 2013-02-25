<?php

namespace Dope\Controller\Request;

class Http extends \Zend_Controller_Request_Http
{
	protected $_contextName = false;
	
	public function getContextName()
	{
		return $this->_contextName;
	}
	
	public function setContextName($contextName)
	{
		$this->_contextName = $contextName;
		return $this;
	}
	
	public function setRequestUri($requestUri = null)
	{
		parent::setRequestUri($requestUri);
		$this->determineContext();
		return $this;
	}
	
	public function setControllerName($value)
	{
		if (strpos($value, '.') !== false) {
			$parts = explode('.', $value);
			$this->setContextName(array_pop($parts));
			$value = join('.', $parts);
		}
		
		return parent::setControllerName($value);
	}
	
	public function setActionName($value)
	{
		if (strpos($value, '.') !== false) {
			$parts = explode('.', $value);
			$this->setContextName(array_pop($parts));
			$value = join('.', $parts);
		}
	
		return parent::setActionName($value);
	}
	
	protected function determineContext()
	{
		/* Remove whitespace and explode on commas */
		$acceptParts = explode(',',
			str_replace(' ', '', $this->getHeader('Accept')
		));
		
		/* Test for dojo */
		if (in_array('application/x-dojo-json', $acceptParts)) {
			$this->setContextName('dojo');
		}
		
		/* Test for json */
		elseif (in_array('application/json', $acceptParts)) {
			$this->setContextName('json');
		}
		
		/* Test for rest */
		elseif (in_array('application/x-rest-json', $acceptParts)) {
		    $this->setContextName('rest');
		}
	}
}
