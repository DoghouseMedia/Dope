<?php

class Dope_View_Helper_TimeTextBox extends Zend_Dojo_View_Helper_TimeTextBox
{
	public function timeTextBox($id, $value = null, array $params = array(), array $attribs = array())
    {
    	if (preg_match('/^(\d{2})(\d{2})(\d{2})$/', $value, $matches)) {
    		$value = $matches[1] . ':' . $matches[2] . ':' . $matches[3];
    	}
    	
    	if ($value AND $value[0] != 'T') {
    		$value = 'T' . $value;
    	}
    	
        return parent::timeTextBox($id, $value, $params, $attribs);
    }
}