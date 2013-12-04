<?php

namespace Dope\Controller\Action\Helper;

/*
 * If the context is deferred (client did not specify) and
 * it's not coming from an app, that means a deep link is being accessed directly.
 * In this case, we want to load the app, then go to that link within the app.
 */

class PushState extends \Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Hook into action controller preDispatch() workflow
	 *
	 * @return void
	 */
	public function preDispatch()
	{
        if ($this->usePushState()) {
            $this->getResponse()->setRedirect($this->getUrl());
        }
	}

    protected function usePushState()
    {
        if (! $this->getActionController() instanceof \Dope\Controller\Action\_Interface\PushState) {
            return false;
        }

        if ($this->getRequest()->getModuleName() == $this->getFrontController()->getDefaultModule()) {
            if ($this->getRequest()->getControllerName() == $this->getFrontController()->getDefaultControllerName()) {
                if ($this->getRequest()->getActionName() == $this->getFrontController()->getDefaultAction()) {
                    return false;
                }
            }
        }

        $contextSwitch = $this->getActionController()->getHelper('ContextSwitch');

        $d = $contextSwitch->isDeferred();
        $a = !$contextSwitch->isFromApp();
        return (bool) ($contextSwitch->isDeferred() AND !$contextSwitch->isFromApp());
    }

    protected function getUrl()
    {
        $url = '/#';

        if ($this->getRequest()->getModuleName() != $this->getFrontController()->getDefaultModule()) {
            $url .= '/' . $this->getRequest()->getModuleName();
        }

        if ($this->getRequest()->getControllerName() != $this->getFrontController()->getDefaultControllerName()) {
            $url .= '/' . $this->getRequest()->getControllerName();
        }

        if ($this->getRequest()->getActionName() != $this->getFrontController()->getDefaultAction()) {
            $url .= '/' . $this->getRequest()->getActionName();
        }

        foreach ($this->getRequest()->getParams() as $key => $value) {
            if (in_array($key, array('module', 'controller', 'action'))) {
                continue;
            }
            $url .= '/' . urlencode($key) . '/' . urlencode($value);
        }

        return $url;
    }
}
