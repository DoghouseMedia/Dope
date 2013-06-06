<?php

class Dope_Form_Element_SearchFilterAdd
extends Zend_Dojo_Form_Element_FilteringSelect
{
	public $helper = 'SearchFilterAdd';
	
	public function setSearchFilters($searchFilters)
	{
		$this->setDijitParam('searchFilters', $searchFilters);
		return $this;
	}
	
	public function init()
	{
		parent::init();
		$this->setDecorators(array('DijitElement'));
	}
}