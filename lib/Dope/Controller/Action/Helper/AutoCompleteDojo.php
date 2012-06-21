<?php

namespace Dope\Controller\Action\Helper;

class AutoCompleteDojo extends \Zend_Controller_Action_Helper_AutoCompleteDojo
{
	public function prepareAutoCompletion($data, $keepLayouts = false)
	{
		if (!$data instanceof \Zend_Dojo_Data) {
            $items = array();
            
            foreach ($data as $key => $value) {
				$items[] = array('name' => $value, 'key' => $key);
            }
            
            $data = new \Zend_Dojo_Data('key', $items, 'name');
        }
        
        return parent::prepareAutoCompletion($data, $keepLayouts);
	}
}