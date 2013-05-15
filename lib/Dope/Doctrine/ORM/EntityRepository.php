<?php

namespace Dope\Doctrine\ORM;

use Doctrine\ORM\Mapping\UniqueConstraint,
    Dope\Entity\Search,
    Dope\Entity\Definition,
    Dope\Controller\Data,
    Dope\Config\Helper as Config;

class EntityRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * @var \Dope\Entity\Search\Table\Aliases
	 */
	protected $tableAliases;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	protected $select;
	
	/**
	 * @var \Dope\Entity\Search
	 */
	protected $search;
	
	/**
	 * @var bool
	 */
	protected $usePagination = false;
	
	/**
	 * @var \Dope\Entity\Definition
	 */
	protected $definition = null;
	
	/**
	 * @return \Dope\Entity\Definition
	 */
	public function getDefinition()
	{
	    if (! $this->definition instanceof Definition) {
	        $this->definition = new Definition($this->getClassName());
	    }
	
	    return $this->definition;
	}
	
	/**
	 * @return \Dope\Entity
	 */
	public function newInstance()
	{
		$className = $this->getClassName();
		return new $className();
	}
	
	public function hasSubClasses()
	{
		return (bool) $this->getSubClasses();
	}
	
	public function getSubClasses()
	{
		$subClasses = array_values($this->getClassMetadata()->discriminatorMap);
		
		array_splice(
			$subClasses,
			array_search($this->getClassName(), $subClasses),
			1
		);
		
		for($i=0; $i < count($subClasses); $i++) {
			$subParentClasses = \Dope\Doctrine::getEntityManager()
				->getClassMetadata($subClasses[$i])->parentClasses;
			
			if (! in_array($this->getClassName(), $subParentClasses)) {
				array_splice($subClasses, $i, 1);
				$i--;
			}
		}
		
		\Dope\Log::console($subClasses);
		
		return $subClasses;
	}
	
	public function getSubClassTables()
	{
		return array_map(function($subClass) {
			return \Dope\Doctrine::getRepository($subClass);
		}, $this->getSubClasses());
	}
	
	public function hasColumn($columnName)
	{
		return in_array($columnName, $this->getColumnNames());
	}
	
	public function getModelAlias($className=null)
	{
		$className = $className ?: $this->getClassMetadata()->rootEntityName;
		
		return strtolower(str_replace(
			$this->getClassMetadata()->namespace . '\\',
			'',
			$className
		));
	}
	
	public function getModelKey()
	{
		return $this->getModelAlias($this->getClassName());
	}
	
	public function getAssociationMappings()
	{
		return $this->getClassMetadata()->getAssociationMappings();
	}

	public function getTableAliases()
	{
		if (! $this->tableAliases instanceof Search\Table\Aliases) {
			$this->tableAliases = new Search\Table\Aliases();
		}
	
		return $this->tableAliases;
	}
	
	public function isColumnUnique($columnName) {
		foreach($this->getIndexes() as $indexName => $indexData) {
			$isTypeUnique = isset($indexData['type']) && ($indexData['type'] == 'unique');
			$isColInIndex = isset($indexData['fields']) && in_array($columnName, array_keys($indexData['fields']));
			
			if ($isTypeUnique AND $isColInIndex) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @return \Dope\Entity\Search
	 */
	public function getSearch()
	{
		return $this->search;
	}
	
	public function usePagination($usePagination=null)
	{
		if (is_bool($usePagination)) {
			$this->usePagination = $usePagination;
		}
		
		return $this->usePagination;
	}
	
	/**
	 * 
	 */
	public function useFileTypeField()
	{
		return true;
	}
    
    /**
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata()
    {
    	return parent::getClassMetadata();
    }
    
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
    	return parent::getEntityManager();
    }
    
    public function getColumnNames(array $fieldNames = null)
    {
    	return array_merge(
    		$this->getClassMetadata()->getColumnNames($fieldNames),
    		$this->getClassMetadata()->getAssociationNames()
    	);
    }

    public function getIrrelevantColumnNames()
    {
    	return array(
    		'editable',
    		'deleted'
    	);
    }
	
    /**
     * Returns default sort (eg. "field_name DESC") or false
     */
    public function getDefaultSort()
    {
    	return false;
    }
    
	/**
	 * Retrieve form/input filter
	 * 
	 * @todo Somehow it doesn't feel right having this here...
	 * 
	 * @return \Dope\Form\Entity
	 */
	public function getForm(array $options=array(), $prefix = null, $alias=null, $default='\Dope\Form\Entity', $depth=0)
	{	
		if (is_null($prefix)) {
			$prefix = '\\' . Config::getOption('appnamespace') . '\Form\Entity';
		}

		$alias = $alias ?: $this->getModelAlias($this->getClassName());
		
		$form = null;
		$inflector = new \Zend_Filter_Word_UnderscoreToCamelCase();
		$formclass = $prefix . '\\' . $inflector->filter($alias);

		/*
		 * Yuck, we need to turn off error reporting since 
		 * trying to autoload will spit out Warnings!
		 * 
		 * @todo There are very few models that don't have a form,
		 * so creating a default form might be a better solution.
		 */
		$errorReporting = error_reporting(0);
		if (\Zend_Loader_Autoloader::autoload($formclass)) {
			$form = new $formclass();
		}
		error_reporting($errorReporting);
		
		if (! $form instanceof \Dope\Form\_Base) {
			if (isset($this->getClassMetadata()->parentClasses[$depth])) {
				$alias = $this->getModelAlias($this->getClassMetadata()->parentClasses[$depth]);
				return $this->getForm($options, $prefix, $alias, $default, $depth+1);
			}
			
			$form = new $default();
		}
		
		$form->setOptions($options);
		
		return $form;
	}
	
	
	public function getForeignKeyRelationEntity($key)
	{
		foreach($this->getAssociationMappings() as $alias => $mapping) {
			if (isset($mapping['joinColumns']) AND $mapping['joinColumns'][0]['name'] == $key) {
				return $mapping['targetEntity'];
			}
		}
		
		return false;
	}
}
