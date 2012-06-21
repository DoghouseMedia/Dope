<?php

namespace Dope\Controller\Action\Helper;

class Json extends \Zend_Controller_Action_Helper_Json
{
	/**
	 * Overriden to address this bug:
	 * @see http://framework.zend.com/issues/browse/ZF-4134
	 * @see \Zend_Controller_Action_Helper_Json::sendJson()
	 */
	public function sendJson($data, $keepLayouts = false)
    {
        $data = $this->encodeJson($data, $keepLayouts);
        $response = $this->getResponse();
        $response->setBody($data);

        if (!$this->suppressExit) {
        	\Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush(); // send FirePHP headers
            $response->sendResponse();
            exit;
        }

        return $data;
    }
}
