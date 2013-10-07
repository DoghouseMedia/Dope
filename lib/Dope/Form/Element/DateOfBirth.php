<?php

require_once 'Dope/Form/Element/Date.php';

class Dope_Form_Element_DateOfBirth
extends Dope_Form_Element_Date
{
	public function init()
	{
		parent::init();
		$this->setValue('');
	}
}