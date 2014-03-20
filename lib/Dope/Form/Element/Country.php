<?php

class Dope_Form_Element_Country
extends Zend_Dojo_Form_Element_ComboBox
{
	public function init()
	{
		parent::init();
		
		$countries = Zend_Locale::getTranslationList('territory', 'en_AU', 2);
		$countries[""] = "";
		
		asort($countries);
		
		$this->setMultiOptions(array_combine(
            $countries,
            $countries
        ));
	}
}