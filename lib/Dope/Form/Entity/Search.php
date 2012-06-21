<?php

namespace Dope\Form\Entity;
use Dope\Form\Entity,
	Dope\Form\Entity\Search;

class Search extends \Dope\Form\_Base
{
	public function init()
	{
		parent::init();
		
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('class' => 'main')),
			'SearchFilters',
			'SearchForm'
		));
	}
	
	public function preConfigure()
	{
		parent::preConfigure();
		
		if ($this->hasController()) {
			/* Action URL */
			$this->setAction(
				$this->getController()->getRequest()->getControllerName()
			);
		}
	}
	
	public function postConfigure()
	{
		parent::postConfigure();
		$this->getElement('submit')->setLabel("Search");
	}
	
	public function setView(\Zend_View $view)
	{
		parent::setView($view);
		$this->setElementsBelongTo('');
		return $this;
	}
	
	public function getFilters()
	{
		$filters = array();
	
		foreach($this->getEntityRepository()->getAssociationMappings() as $alias => $mapping) {
			$filters[] = new Search\Filter(
				$mapping['fieldName'],
				ucfirst($mapping['fieldName']),
				$mapping['targetEntity']
			);
		}
	
		return $filters;
	}
}