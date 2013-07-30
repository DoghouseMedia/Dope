<?php

namespace Dope\Doctrine\ORM;

use Doctrine\ORM\Mapping\UniqueConstraint,
	Dope\Entity,
    Dope\Entity\Search,
    Dope\Entity\Definition,
    Dope\Controller\Data,
    Dope\Config\Helper as Config,
	Zend_Loader_Autoloader as Autoloader;

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
		$inflector = new \Zend_Filter_Word_UnderscoreToCamelCase();
		
		$prefix = is_null($prefix) ? '\\' . Config::getOption('appnamespace') . '\Form\Entity' : $prefix;
		$alias = $alias ?: $this->getModelAlias($this->getClassName());
		$formclass = $prefix . '\\' . $inflector->filter($alias);
		$formclassShort = ltrim($formclass, '\\');
		$form = null;
		
		/*
		 * Find the good autoloaders and load the class specific form if it exists
		 */
		foreach (Autoloader::getInstance()->getClassAutoloaders($formclassShort) as $autoloader) {
			if ($autoloader[0]->canLoadClass($formclassShort)) {
				$form = new $formclass();
				break;
			}
		}
		
		/*
		 * If we haven't found a form yet, use the generic one
		 */
		if (! $form instanceof \Dope\Form\_Base) {
			/*
			 * If this is a subclass, try to get the parent form before defaulting to the generic one.
			 */
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
	
	public function flatten(array $data, $forceShallow=false, $withEntityIds=true)
	{
		$array = array();
		$md = $this->getClassMetadata();
		$keys = array_merge(
	        $md->getFieldNames(),
	        $md->getAssociationNames()
		);
		
		foreach ($keys as $key) {
			if (! isset($data[$key])) {
				continue;
			}
			
		    if ($forceShallow AND $data[$key] instanceof \DateTime) {
		        switch ($md->getTypeOfColumn($key)) {
		            case 'time':
		                $array[$key] = $data[$key]->format("H:i:s");
		                break;
		            case 'date':
		                $array[$key] = $data[$key]->format("Y-m-d");
		                break;
		            case 'datetime':
		            default:
		                $array[$key] = $data[$key]->format("Y-m-d H:i:s");
		                break;
		        }
		    }
		    elseif ($forceShallow AND $withEntityIds AND $data[$key] instanceof \Doctrine\Common\Collections\Collection) {
		        $_key = $key . '_ids';
		        $array[$_key] = array();
		        foreach ($data[$key] as $_entity) {
		            $array[$_key][] = $_entity->id;
		        }
		    }
		    elseif ($data[$key] instanceof Entity) {
		        // Force load
		        (string) $data[$key];
		         
		        if ($forceShallow) {
		            $array[$key] = trim((string) $data[$key]);
		        }
		        if ($withEntityIds) {
		        	$array[$key . '_id'] = (int) $data[$key]->id;
		        }
		    }
		    else {
		        $array[$key] = is_string($data[$key]) ? trim($data[$key]) : $data[$key];
		    }
		}
		
		if ($md->discriminatorValue) {
		    $array['dtype'] = $md->discriminatorValue;
		}
		
		return $array;
	}
}
