<?php

namespace Dope\Form\Entity;
use Dope\Entity\Definition;

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
				'/' . $this->getController()->getRequest()->getControllerName()
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
		$mappings = $this->getEntityRepository()->getAssociationMappings();
		$definition = new Definition($this->getEntityRepository()->getClassName());
		$searchFilters = $definition->getSearchFilters()->value;
		
		foreach ($searchFilters as $value) {
		    if (!is_array($value)) {
		        $value = array(
		            'name' => $mappings[$value]['fieldName'],
	                'label' => ucfirst($mappings[$value]['fieldName']),
	                'target' => $mappings[$value]['targetEntity'],
                    'sort' => isset($mappings[$value]['sort']) ? $mappings[$value]['sort'] : ''
		        );
		    }	
		    	    
			$filters[] = new Search\Filter(
				$value['name'],
				$value['label'],
				$value['target'],
                isset($value['sort']) ? $value['sort'] : ''
			);
		}
	
		return $filters;
	}
}