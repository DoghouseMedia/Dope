<?php

class Dope_Form_Element_ResidentStatus
extends Zend_Dojo_Form_Element_ComboBox
{
	public function init()
	{
		parent::init();
		$this->setMultiOptions(array(
			''		=> '',
		 	'C'	=> 'Australian citizen',
		 	'R'	=> 'Australian permanent resident',
			'NZ' => 'New Zealand citizen',
			'V'	=> 'Visa'
		));
	}
}