<?php

class Dope_Form_Element_Country
extends Zend_Dojo_Form_Element_FilteringSelect
{
	public function init()
	{
		parent::init();
		
		$countries = Zend_Locale::getTranslationList('territory', 'en_AU', 2);
		$countries[""] = "";
		
		asort($countries);
		
		$this->setMultiOptions($countries);
	}
}