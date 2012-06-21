<?php

require_once 'ValidationTextBox.php';

class Dope_Form_Element_YesNo
extends Zend_Dojo_Form_Element_ComboBox
{
	public function init()
	{
		parent::init();
		$this->setMultiOptions(array(
			'0' => 'No',
			'1' => 'Yes'
		));
	}
}