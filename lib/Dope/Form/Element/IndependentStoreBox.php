<?php

require_once 'Dope/Form/Element/StoreBox.php';

class Dope_Form_Element_IndependentStoreBox
extends Dope_Form_Element_StoreBox
{
	public $helper = 'StoreBox';
	
	public function init()
	{
		parent::init();
		
		$this->setDijitParams(array(
			'deaf' => 'true',
			'noisy' => 'false'
		));
	}
}