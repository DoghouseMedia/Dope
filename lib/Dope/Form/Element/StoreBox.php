<?php

class Dope_Form_Element_StoreBox
extends Zend_Dojo_Form_Element_FilteringSelect
{
	public $helper = 'StoreBox';
	
	public function init()
	{
		parent::init();
		
		$prefix = $this->getAttrib('storePrefix') ?: $this->getName();
		
		$this
			->setStoreId($prefix . 'Store')
			->setStoreType('dope.data.ItemFileReadStore')
			->setStoreParams(array('url' => '/' . $prefix . '/autocomplete'))
			->setAutocomplete(false)
			->setAttrib('storeController', $prefix);
	}
}