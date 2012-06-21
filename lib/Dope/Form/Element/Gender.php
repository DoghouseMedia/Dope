<?php

class Dope_Form_Element_Gender
extends Zend_Dojo_Form_Element_ComboBox
{
	public function init()
	{
		parent::init();
		$this->setMultiOptions(array(
			''		=> '',
		 	'M'	=> 'Male',
			'F'	=> 'Female'
		));
	}
}