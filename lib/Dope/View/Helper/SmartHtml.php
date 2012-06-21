<?php

class Dope_View_Helper_SmartHtml extends Zend_View_Helper_FormElement
{
	public function smartHtml($name, $value = null, $attribs = null, $options = null)
    {
    	if ($value) {
    		$html  = '<div' . $this->_htmlAttribs($attribs) . '>';
    		$html .= $value;
    		$html .= '</div>';
    		
    		return $html;
    	}
    	
    	$parentForm = $attribs['params']['form'];
    	
    	while($parentForm instanceof \Dope\Form\Entity\Subform AND $parentForm->hasParentForm()) {
    		$parentForm = $parentForm->getParentForm();
    	}
    	
    	return $this->view
    		->assign('form', $parentForm)
    		->render($attribs['params']['viewScript']);
    }
}