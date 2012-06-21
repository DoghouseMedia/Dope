<?php

class Dope_Form_Element_MaritalStatus
extends Zend_Dojo_Form_Element_ComboBox
{
	public function init()
	{
		parent::init();
		$this->setMultiOptions(array(
			'' => '',
			'C'	=> 'Deceased',
			'D'	=> 'Divorced',
			'E'	=> 'Engaged',
			'F'	=> 'Defacto',
			'M'	=> 'Married',
			'P'	=> 'Separated',
			'S'	=> 'Single',
			'W'	=> 'Widowed'
		));
	}
}