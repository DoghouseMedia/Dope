<?php

require_once 'ValidationTextBox.php';

class Dope_Form_Element_Text
extends Dope_Form_Element_ValidationTextBox
{
	public function init()
	{
		parent::init();
		$this->setRegExp('\w+.*?');
	}
}