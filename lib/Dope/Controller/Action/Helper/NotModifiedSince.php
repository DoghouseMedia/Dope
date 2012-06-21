<?php

namespace Dope\Controller\Action\Helper;

class NotModifiedSince extends \Zend_Controller_Action_Helper_Abstract
{
	protected function exitIfNotModifiedSince($lastModified)
	{
	    if(array_key_exists("HTTP_IF_MODIFIED_SINCE", $_SERVER)) {
	        $ifModifiedSince = strtotime(preg_replace('/;.*$/', '', $_SERVER["HTTP_IF_MODIFIED_SINCE"]));
	        
	        if($ifModifiedSince >= $lastModified) {
				$this->getResponse()
					->setHttpResponseCode(304)
	            	->sendResponse();
	            exit(0);
	        }
	    }
	    
	    $this->getResponse()
			->setHeader('Cache-Control', 'max-age=36000, must-revalidate', true)
			->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 10 * 3600) . ' GMT', true)
			->setHeader('Last-modified', date('r', $lastModified))
			->setHeader('Pragma', '')
			->clearRawHeaders();
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
    public function direct($lastModified)
    {
        $this->exitIfNotModifiedSince($lastModified);
    }
}
