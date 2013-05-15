<?php

namespace Dope\Controller\Action\Helper;

class Http extends \Zend_Controller_Action_Helper_Abstract
{
	public function preDispatch()
	{
	    /* Range (list_start/list_count) */
	    if ($this->getRequest()->getHeader('Range')) {
	        if (preg_match('/^items=(\d*)-(\d*)$/', $this->getRequest()->getHeader('Range'), $matches)) {
	            $this->getRequest()->setParams(array(
                    // Start
                    'list_start' => $matches[1],
                    // Count: need to add the 1 because of how mysql counts
                    'list_count' => $matches[2] - $matches[1] + 1
	            ));
	        }
	    }
	}
	
	public function setContentRange($rangeStart, $rangeEnd, $rangeTotal)
	{
		/* HTTP 206 Partial Content */
        if (($rangeEnd - $rangeStart) < $rangeTotal) {
            $this->getResponse()->setHttpResponseCode(206);
        }
	
	    /* HTTP Content-Range */
	    $this->getResponse()->setHeader('Content-Range',
	        "items {$rangeStart}-{$rangeEnd}/{$rangeTotal}"
	    );
	}
}
