<?php

namespace Dope\Controller\Action\Helper;

class Url extends \Zend_Controller_Action_Helper_Url
{
    public function simple($action, $controller = null, $module = null, array $params = null)
    {
        return
            '/' .
            parent::simple($action, $controller, $module, $params);
    }
}
