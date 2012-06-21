<?php

require_once 'ValidationTextBox.php';

class Dope_Form_Element_Phone
extends Dope_Form_Element_ValidationTextBox
{
	const REGEXP = "^(\+|\s|\(|\)|[0-9_-])+$";
	
	public function init()
	{
		parent::init();
		$this->setRegExp(static::REGEXP);
	}
}