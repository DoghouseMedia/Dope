<?php

namespace Dope\Controller\Action\Helper;

class View extends \Zend_Controller_Action_Helper_Abstract
{	
	public function init()
	{
		/**
		 * Get the controller object
		 * @var Zend_Controller_Action
		 */
		$controller = $this->getActionController();

		/* Get the bootstrap object */
		$bootstrap = $controller->getInvokeArg('bootstrap');
		
		/**
		 * Get the view object
		 * @var Zend_View
		 */
		$view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		/* Set modelAlias() */
		if ($controller instanceof \Dope\Controller\Action\Model) {
			$view->modelAlias($controller->getModelAlias());
		}
		
		/* Set senderAlias() and senderId() */
		if ($controller instanceof \Dope\Controller\Action) {
			$view->senderAlias($controller->getData()->sender);
			$view->senderId($controller->getData()->{$controller->getData()->sender});
			
			/* Set tabId */
			//$view->tabId($controller->getData()->tab);
		}
		
		/* Set ACL object in ACL view helper */
		$view->isAllowed()->setAcl(\Dope\Auth\Service::getAcl());
		
		/* Dojo settings */
		\Zend_Dojo_View_Helper_Dojo::setUseDeclarative();
		
		/* View script path(s) */		
		$view->setScriptPath(array(
			APPLICATION_PATH . '/views/generic/scripts',
			APPLICATION_PATH . '/views/scripts'
		));
	}
}
