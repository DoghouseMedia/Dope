<?php

class Dope_Form_Element_Date
extends Zend_Dojo_Form_Element_DateTextBox
{
	public function init()
	{
		parent::init();
		$this
			->setFormatLength('long')
			->setInvalidMessage('Invalid date specified')
			->setValue(date('Y-m-d'));
	}
}