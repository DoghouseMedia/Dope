<?php

require_once 'ValidationTextBox.php';

class Dope_Form_Element_Email
extends Dope_Form_Element_ValidationTextBox
{
	const REGEXP = "[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?";
	
	public function init()
	{
		parent::init();
		$this->setRegExp('^' . static::REGEXP . '$');
	}
}