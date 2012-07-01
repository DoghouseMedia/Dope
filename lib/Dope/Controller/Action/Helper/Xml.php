<?php

namespace Dope\Controller\Action\Helper;

class Xml extends \Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Suppress exit when sendXml() called
	 * @var boolean
	 */
	public $suppressExit = false;

	/**
	 * Create XML response
	 *
	 * Encodes and returns data to XML. Content-Type header set to
	 * 'application/xml', and disables layouts and viewRenderer (if being
	 * used).
	 *
	 * @param  mixed   $data
	 * @param  boolean $keepLayouts
	 * @param  boolean|array $keepLayouts
	 * @throws \Zend_Controller_Action_Helper_Xml
	 * @return string
	 */
	public function encodeXml($data, $keepLayouts = false)
	{
		require_once 'Zend/XmlRpc/Response.php';
		$xmlHelper = new \Zend_XmlRpc_Response($data);
		$data = $xmlHelper->saveXml();

		if (!$keepLayouts) {
			/**
			 * @see Zend_Controller_Action_HelperBroker
			 */
			require_once 'Zend/Controller/Action/HelperBroker.php';
			\Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
		}

		return $data;
	}

	public function sendXml($data, $keepLayouts = false)
	{
		$data = $this->encodeXml($data, $keepLayouts);
		
		$response = $this->getResponse();
		$response->setHeader('Content-Type', 'application/xml');
		$response->setBody($data);

		if (!$this->suppressExit) {
			$response->sendResponse();
			exit;
		}

		return $data;
	}

	/**
	 * Strategy pattern: call helper as helper broker method
	 *
	 * Allows encoding XML. If $sendNow is true, immediately sends XML
	 * response.
	 *
	 * @param  mixed   $data
	 * @param  boolean $sendNow
	 * @param  boolean $keepLayouts
	 * @return string|void
	 */
	public function direct($data, $sendNow = true, $keepLayouts = false)
	{
		if ($sendNow) {
			return $this->sendXml($data, $keepLayouts);
		}
		return $this->encodeXml($data, $keepLayouts);
	}
}
