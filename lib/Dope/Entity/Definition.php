<?php

namespace Dope\Entity;

class Definition extends \ReflectionClass
{
	public function __construct($argument)
	{
		parent::__construct($argument);		
		$this->reader = new \Doctrine\Common\Annotations\AnnotationReader();
	}

	public function getAnnotation($annotationClassname, $property=null)
	{
		if ($property) {
			return $this->reader->getPropertyAnnotation(
				$this->getProperty($property),
				$annotationClassname
			);
		}
		else {
			$class = in_array('Doctrine\ORM\Proxy\Proxy', $this->getInterfaceNames()) ?
				$this->getParentClass() :
				$this;
			
			return $this->reader->getClassAnnotation(
				$class,
				$annotationClassname
			);
		}
	}

	public function getColumnNames()
	{
		$array = array();

		foreach ($this->getProperties() as $property) {
			$hasColumn = $this->hasColumn($property->getName());
			$hasManyToOne = $this->hasManyToOne($property->getName());
			
			if ($hasColumn OR $hasManyToOne) {
				$array[] = $property->getName();
			}
		}
		
		return $array;
	}
	
	public function hasColumn($columnName)
	{
		return (bool) $this->getAnnotation('\Doctrine\ORM\Mapping\Column', $columnName);
	}
	
	public function hasManyToOne($columnName)
	{
		return (bool) $this->getAnnotation('\Doctrine\ORM\Mapping\ManyToOne', $columnName);
	}
	
	public function getColumnType($columnName)
	{
		return $this->getAnnotation('\Doctrine\ORM\Mapping\Column', $columnName)->type;
	}
	
	public function getFields()
	{
		$fields = new \ArrayObject();
		
		foreach($this->getColumnNames() as $columnName) {
			$field = $this->getField($columnName);
			if ($field) {
				$fields[$columnName] = $field;
			}
		}
		
		return $fields;
	}
	
	public function hasIndexAnnotation()
	{
		if (! $this->getIndexAnnotation()) {
			return false;
		}
	
		if (! $this->getIndexAnnotation()->entity) {
			return false;
		}
	
		return true;
	}
	
	public function getIndexAnnotation()
	{
		return $this->getAnnotation('\Dope\Doctrine\ORM\Mapping\Index');
	}
	
	public function getField($columnName)
	{
		return $this->getAnnotation('\Dope\Doctrine\ORM\Mapping\Field', $columnName);
	}
	
	public function getGroup($columnName)
	{
		return $this->getAnnotation('\Dope\Doctrine\ORM\Mapping\Group', $columnName);
	}
	
	public function getToStringColumnNames()
	{
		if ($this->getAnnotation('\Dope\Doctrine\ORM\Mapping\Stringify')) {
			return $this->getAnnotation('\Dope\Doctrine\ORM\Mapping\Stringify')->value;
		}
		else {
			return array();
		}
	}
	
	public function getBrowseOptions()
	{
		$browseOptions = $this->getAnnotation('\Dope\Doctrine\ORM\Mapping\BrowseOptions');
		if ($browseOptions instanceof \Dope\Doctrine\ORM\Mapping\BrowseOptions) {
			return $browseOptions;
		} else {
			return new \Dope\Doctrine\ORM\Mapping\BrowseOptions();
		}
	}

	public function factory($params)
	{
		$instance = $this->newInstance();
		
		foreach($this->getColumnNames() as $columnName) {
			if (isset($params[$columnName])) {
				$instance->$columnName = $params[$columnName];
			}
		}
		
		return $instance;
	}
}