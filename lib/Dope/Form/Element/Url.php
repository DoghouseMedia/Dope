<?php

require_once 'ValidationTextBox.php';

class Dope_Form_Element_Url
extends Dope_Form_Element_ValidationTextBox
{
	const REGEXP = "^https?:\\/\\/.*$";
	
	public function init()
	{
		parent::init();
		$this->setRegExp(static::REGEXP);
	}
}