<?php

class Dope_Form_Element_SmartHtml
extends Zend_Form_Element_Xhtml
{
	public $helper = 'SmartHtml';
	
	public function init()
    {
    	parent::init();
    	
    	$this->setDecorators(array('ViewHelper'));
	}
}
