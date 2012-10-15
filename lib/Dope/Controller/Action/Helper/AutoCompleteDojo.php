<?php

namespace Dope\Controller\Action\Helper;

class AutoCompleteDojo extends \Zend_Controller_Action_Helper_AutoCompleteDojo
{
	public function prepareAutoCompletion($data, $keepLayouts = false)
	{
		if (!$data instanceof \Zend_Dojo_Data) {
            if (count($data) AND !is_array(current($data))) {
            	$items = array();
	            foreach ($data as $key => $value) {
					$items[] = array('__toString' => $value, 'id' => $key);
	            }
	            $data = $items;
            }

            $data = new \Zend_Dojo_Data('id', $data, '__toString');
        }
        
        return parent::prepareAutoCompletion($data, $keepLayouts);
	}
}