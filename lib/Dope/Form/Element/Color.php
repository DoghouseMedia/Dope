<?php

//require_once 'ValidationTextBox.php';

class Dope_Form_Element_Color
extends Zend_Form_Element_Text
{
	public function init()
	{
		parent::init();
		$this->setAttrib('class', 'color-picker');
	}
}