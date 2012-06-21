<?php

require_once 'NumberTextBox.php';

class Dope_Form_Element_Decimal
extends Dope_Form_Element_NumberTextBox
{
	public function init()
	{
		parent::init();
		$this->setRegExp('^\d+(?:\.\d+)?$');
		$this->setType('decimal');
	}
}