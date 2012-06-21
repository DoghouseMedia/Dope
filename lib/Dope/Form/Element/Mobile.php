<?php

require_once 'ValidationTextBox.php';

class Dope_Form_Element_Mobile
extends Dope_Form_Element_ValidationTextBox
{
	const REGEXP = "^(?:\+(?:\d(?:\s*)){8,}|04\s*(?:\d(?:\s*)){8})$";
	
	public function init()
	{
		parent::init();
		// AU mobile OR international format
		$this->setRegExp(static::REGEXP);
	}
}