<?php

class Dope_Form_Element_Year
extends Zend_Dojo_Form_Element_FilteringSelect
{
	public function init()
	{
		parent::init();
		
		$years = range(1960, date('Y')+10); // from 1960 to now+10years
		array_unshift($years, ''); // add empty selection

		$this->setMultiOptions(array_combine($years, $years));
	}
}