<?php

namespace Dope\Controller\Plugin;

class RawJsonHandler extends \Zend_Controller_Plugin_Abstract
{
	/**
	 * Before dispatching, digest raw JSON request body and set params
	 *
	 * @param \Zend_Controller_Request_Abstract $request
	 */
	public function preDispatch(\Zend_Controller_Request_Abstract $request)
	{
		if (!$request instanceof \Zend_Controller_Request_Http) {
			return;
		}
	
		if (!$request->isPut() AND !$request->isPost()) {
			return;
		}
		
		/* Assign Json raw data to params */
		try {
			$jsonData = \Zend_Json::decode($request->getRawBody(), \Zend_Json::TYPE_OBJECT);
			if ($jsonData) {
				foreach($jsonData as $key => $val) {
					$this->getRequest()->setParam($key, $val);
				}
			}
		} catch(\Exception $e) {}
	}
}
