<?php

class Dope_Form_Element_Time
extends Zend_Dojo_Form_Element_TimeTextBox
{
	public function init()
	{
		parent::init();
		$this->setAttrib('filters', array('Digits'));
	}
}