<?php

namespace Dope\Controller\Plugin;

use Dope\Auth\Service as Auth;
use Dope\Env;

class RestTokenAuth extends \Zend_Controller_Plugin_Abstract
{
	/**
	 * Before dispatching, digest raw request body and set params
	 *
	 * @param \Zend_Controller_Request_Abstract $request
	 */
	public function preDispatch(\Zend_Controller_Request_Abstract $request)
	{
		if (!$request instanceof \Zend_Controller_Request_Http) {
			return;
		}

        if (Env::isCLI()) {
            return;
        }

        $this->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setHeader('Access-Control-Allow-Headers', join(',', array(
            'Dope-Rest-Token',
            'Range',
            'X-Requested-With',
            'Content-type',
            'Dope-Accept'
        )));
        $this->getResponse()->setHeader('Access-Control-Expose-Headers', join(',', array(
            'Content-Range',
            'Dope-Search-Id',
            'Link'
        )));

        if ('OPTIONS' == strtoupper($request->getMethod())) {
            $this->getResponse()->sendHeaders();
            die();
        }

        $token = $request->getHeader('Dope-Rest-Token');
        if (!$token OR Auth::hasUser()) {
            return;
        }
	
        $user = \Dope\Doctrine::getRepository(Auth::getUserEntityClass())->findOneBy(array(
            'token' => $token
        ));

        if (! $user) {
            return;
        }

        Auth::setUser($user);
	}
}
