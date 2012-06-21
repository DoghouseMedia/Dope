<?php

class Dope_Form_Element_AUState
extends Zend_Dojo_Form_Element_ComboBox
{
	public function init()
	{
		parent::init();
		$this->setMultiOptions(array(
			'' => '',
			'ACT' => 'ACT',
			'QLD' => 'QLD',
			'NSW' => 'NSW',
			'NT' => 'NT',
			'SA' => 'SA',
			'TAS' => 'TAS',
			'VIC' => 'VIC',
			'WA' => 'WA'
		));
	}
}