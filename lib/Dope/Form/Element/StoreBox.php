<?php

class Dope_Form_Element_StoreBox
extends Zend_Dojo_Form_Element_FilteringSelect
{
	public $helper = 'StoreBox';
	
	public function init()
	{
		parent::init();
		
		$prefix = $this->getAttrib('storePrefix') ?: $this->getName();
		$url = $this->getAttrib('storeUrl') ?: "/$prefix/autocomplete";
		
		$this
			->setStoreType('dope.data.ItemFileReadStore')
			->setAttrib('storeController', $prefix)
			->setStoreParams(array('url' => $url))
			->setStoreId($prefix . 'Store')
			->setAutocomplete(false);
	}
	
	public function setStoreParams($params)
	{
		return parent::setStoreParams(array_merge(
			$this->getStoreParams(),
			$params
		));
	}
}