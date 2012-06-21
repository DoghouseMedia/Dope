<?php

namespace Dope\Form\Element\Js;
use \Dope\Form\_Base;

class Template extends \Zend_Form_Element
{
    public $helper = 'formJsTemplate';
    
    public function setForm(_Base $form)
    {
    	$form->setAttribs(array(
    		'id' => '',
    		'name' => '__FORMPREFIX__[' . $form->getName() . '][__INDEX__]'
    	));
    	
    	if ($form->getElementsBelongTo()) {
    		$form->setElementsBelongTo(
    			$form->getName()
    		);
    	}
    	
    	$this->setAttrib('form', $form);
    	
    	return $this;
    }
}