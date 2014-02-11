<?php

require_once 'Decimal.php';

class Dope_Form_Element_Percent
extends Dope_Form_Element_Decimal
{
	public function init()
	{
		parent::init();
		$this->setType('percent');
		$this->setPlaces(2);
	}
}