<?php

class Dope_Form_Element_EmptyDate
extends Dope_Form_Element_Date
{
	public function init()
	{
		parent::init();
		$this->setValue('');
	}
}