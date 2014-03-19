<?php

namespace Dope\Controller\Action\Helper;

use Dope\Env;

class ViewRenderer extends \Zend_Controller_Action_Helper_ViewRenderer
{
	public function render($action = null, $name = null, $noController = null)
    {
    	try {
			return parent::render($action, $name, $noController);
    	}
    	catch (\Zend_View_Exception $e) {
            if (Env::isCLI()) {
                throw $e;
            }
            else {
                $noController = ! $noController;
                return parent::render($action, $name, $noController);
            }
    	}
    	catch (\Exception $e) {
    		throw $e;
    	}
    }
}