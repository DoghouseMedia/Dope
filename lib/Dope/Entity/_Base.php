<?php

namespace Dope\Entity;

use Dope\Entity,
	Doctrine\ORM\Mapping as ORM,
	Doctrine\ORM\EntityManager,
	Dope\Config\Helper as Config,
	Dope\Doctrine\ORM\EntityRepository,
	Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class _Base
implements \IteratorAggregate
{
	/**
	 * @var \Dope\Entity\Definition
	 */
	protected $definition = null;
	
	/**
	 * @var bool $inSaveLoop
	 */
	protected static $inSaveLoop = false;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{	
		$md = \Dope\Doctrine::getEntityManager()->getClassMetadata(get_class($this));
		
		/*
		 * Doctrine requires you to instantiate a placeholder for
		 * each "many" relationship by using the PersistentColection class
		 * in the constructor.
		 * 
		 * To simplify things, the base constructor parses the associations
		 * and instantiates collections automatically.
		 */
		foreach ($md->getAssociationMappings() as $alias => $mapping) {
			$typeNeedsCollection = in_array($mapping['type'], array(
				\Doctrine\ORM\Mapping\ClassMetadata::ONE_TO_MANY,
				\Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_MANY
			));
			
			if ($typeNeedsCollection) {
				\Dope\Log::console("Init new collection $alias on " . get_class($this));
				$this->{$alias} = new ArrayCollection();
			}
		}
	}
	
	public function __call($methodName, $args)
	{
		$action = substr($methodName, 0, 3);
		$key = lcfirst(substr($methodName, 3));
		
		switch($action) {
			case 'get': return $this->__get($key); break;
			case 'set': return ($this->__set($key, $args[0])); break;
			case 'has': return isset($this->$key); break;
		}
		
		throw new \Exception("Method $methodName() does not exist");
	}
	
	public function __isset($key)
	{
		return isset($this->$key);
	}
	
	public function __get($key)
	{
		$getMethodName = 'get' . ucfirst($key);
		
		if (method_exists($this, $getMethodName)) {
			return $this->$getMethodName();
		}
		else {
			return $this->$key;
		}
	}
	
	public function __set($key, $val)
	{
		$setMethodName = 'set' . ucfirst($key);
		
		if (method_exists($this, $setMethodName)) {
			return $this->$setMethodName($val);
		}
		else {
			$this->$key = $val;
			return $this;	
		}
	}
	
	public function replicate()
	{
		$replica = clone $this;	
		
		/* Unset ID */
		$replica->id = null;
		
		return $replica;
	}
	
	public function getEntityKey()
	{
		/*
		 * end() expects a reference, so we can't "chain" the calls but are forced to assign
		 * the output of explode() to avariable first
		 * @see http://stackoverflow.com/questions/4636166/only-variables-should-be-passed-by-reference
		 */
	    $classParts = explode('\\', get_class($this));
		return strtolower(end($classParts));
	}
	
	public function getRepository()
	{
		return \Dope\Doctrine::getRepository(get_class($this));
	}
	
	/**
	 * @return \Dope\Entity\Definition
	 */
	public function getDefinition()
	{
		if (! $this->definition instanceof Definition) {
			$this->definition = new Definition($this);
		}
		
		return $this->definition;
	}
	
	public function isDeleted()
	{
		return (bool) $this->deleted;
	}
	
	public function isEditable()
	{
		return true;
	}
	
	public function isModifiedColumn($key)
	{
		$changeSet = $this->getModifiedColumns();
		return isset($changeSet[$key]);
	}
	
	public function getModifiedColumns()
	{
		$em = \Dope\Doctrine::getEntityManager();
		$md = $em->getClassMetadata(get_class($this));
		$uow = $em->getUnitOfWork();
		
		if ($uow->getEntityState($this) == \Doctrine\ORM\UnitOfWork::STATE_MANAGED) {
			$uow->computeChangeSet($md, $this);
		}
		
		return $uow->getEntityChangeSet($this);
	}
	
	public function getIterator() {
		return new \ArrayIterator($this->toArray());
	}
	
	public function toArray($flatten=true)
	{
		$array = array();
		
		$md = \Dope\Doctrine::getEntityManager()->getClassMetadata(get_class($this));
		
		$keys = array_merge(
			$md->getFieldNames(),
			$md->getAssociationNames()
		);
		
		foreach ($keys as $key) {
			if ($flatten && $this->{$key} instanceof \DateTime) {
				switch ($md->getTypeOfColumn($key)) {
					case 'time':
						$array[$key] = $this->$key->format("H:i:s");
						break;
					case 'date':
						$array[$key] = $this->$key->format("Y-m-d");
						break;
					case 'datetime':
					default:
						$array[$key] = $this->$key->format("Y-m-d H:i:s");
						break;
				}
			}
			elseif ($this->$key instanceof \Doctrine\Common\Collections\Collection) {
			    $_key = $key . '_ids';
			    $array[$_key] = array();
			    foreach ($this->$key as $_entity) {
			        $array[$_key][] = $_entity->id;
			    }
			}
			elseif ($this->$key instanceof Entity) {
			    // Force load
			    (string) $this->$key;
			    
			    if ($flatten) {
				    $array[$key] = (string) $this->$key;
			    }
				$array[$key . '_id'] = (int) $this->$key->id;
			}
			else {
				$array[$key] = $this->$key;
			}
		}
		
		if ($md->discriminatorValue) {
		    $array['dtype'] = $md->discriminatorValue;
		}

		return $array;
	}
	
// 	protected function _setDate($key, $mixed)
// 	{
// 		$value = null;
		
// 		if (is_string($mixed)) {
// 			$value = new \DateTime($mixed);
// 		}
// 		elseif (is_int($mixed)) {
// 			$value = new \DateTime();
// 			$value->setTimestamp($mixed);
// 		}
		
// 		if (! $value instanceof \DateTime) {
// 			throw new \Exception(
// 				"Date for $key must be either a DateTime object" . 
// 				"or a string or int we can transform to one."
// 			);
// 		}
		
// 		$this->$key = $value;
// 		return $this;
// 	}
	
	/**
	 * Save
	 * 
	 * @return \Dope\Entity\_Base
	 */
	public function save()
	{
		/* Entity Manager */
		$em = \Dope\Doctrine::getEntityManager();
		
		/* Persist */
		$em->persist($this);
		
		/* Flush */
		\Dope\Doctrine::flush($this);
		
		return $this;
	}
	
	public function inSaveLoop($inSaveLoop=null)
	{
		if (is_bool($inSaveLoop)) {
			static::$inSaveLoop = $inSaveLoop;
		}
		
		return (bool) static::$inSaveLoop;
	}
	
	/**
	 * Delete
	 * 
	 * @return \Dope\Entity\_Base
	 */
	public function delete()
	{
		$this->deleted = true;
		return $this->save();
	}
	
	/**
	 * Save from array
	 *
	 * @param array $array
	 */
	public function saveFromArray(array $array)
	{
		return $this
			->assignFromArray($array)
			->save();
	}
	
	/**
	 * Assign from array
	 *
	 * @param array $array
	 * @return \Dope\Entity\_Base
	 */
	public function assignFromArray(array $array)
	{
		$columns = array();
		$relations = array();
		
		$md = \Dope\Doctrine::getEntityManager()->getClassMetadata(get_class($this));
		
		$keys = array_merge(
	        $md->getFieldNames(),
	        $md->getAssociationNames()
		);

		foreach ($keys as $key) {
		    
		    $key = rtrim($key, 's');
		    $keyPlural = $key . 's';
		    $val = isset($array[$key]) ? $array[$key] : null;
		    $valKeyPlural = isset($array[$keyPlural]) ? $array[$keyPlural] : $val;
		    
		    if (!isset($array[$key]) AND !isset($array[$keyPlural])) {
		        continue;
		    }
		    
			/* Save field, eg. $this->whatever */
			if ($md->hasField($key)) {
				switch ($md->getTypeOfColumn($key)) {
					case 'time':
					case 'date':
					case 'datetime':
						if (! $val instanceof \Datetime) {
							$val = (is_string($val) AND strlen($val) > 0)
								? new \DateTime($val)
								: null;
						}
						break;
				}
			
				$this->__set($key, $val);
			}
			/* Save single relation, eg. $this->user */
			elseif ($md->hasAssociation($key)) {
			    $this->assignFromRelation($key, $val);
			}
			/* Save multiple relation, eg. $this->categories */
			elseif ($md->hasAssociation($keyPlural)) {
			    $this->assignFromRelation($keyPlural, $valKeyPlural);
			}
			/* this field doesn't exist... */
			else {
				/*
				 * Non-existing fields handling
				 * 
				 * @todo We used to save non-existent fields to some kind
				 * of "meta" functionality. I can't remember what this did though.
				 * Figure out why this was here, and uncomment/refactor or remove!
				 */
				//$this->meta($key, $val);
			}
		}
	
		return $this;
	}
	
	protected function assignFromRelation($key, $val)
	{
	    $md = \Dope\Doctrine::getEntityManager()->getClassMetadata(get_class($this));
	    $mapping = $md->getAssociationMapping($key);
	    $typeIsCollection = in_array($mapping['type'], array(
            \Doctrine\ORM\Mapping\ClassMetadata::ONE_TO_MANY,
            \Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_MANY
	    ));
	     
	    if ($typeIsCollection) {
	        $vals = (strpos($val, ',') !== false) ? explode(',', $val) : array($val);
	    
	        foreach ($vals as $_val) {
	            if ($_val == '') {
	                continue;
	            }
	             
	            /*
	             * We don't know which side is owning side, so we save on both.
	             * @todo Read up on owning side and shorten/simplify this.
	             */
	             
	            $targetRecord = \Dope\Doctrine::getRepository($mapping['targetEntity'])->find($_val);
	             
	            /* Skip duplicates */
	            if ($this->{$key} instanceof \Doctrine\Common\Collections\Collection) {
	                if ($this->{$key}->contains($targetRecord)) {
	                    continue;
	                }
	            }
	            elseif (is_array($this->{$key})) {
	                if (in_array($targetRecord, $this->{$key})) {
	                    continue;
	                }
	            }
	             
	            /* Save our side */
	            $this->{$key}[] = $targetRecord;
	             
	            /* Save other side */
	            $targetField = $mapping['mappedBy'];
	            if ($targetField) {
	                if (isset($targetRecord->{$targetField})) {
	                    $targetRecord->{$targetField}[] = $this;
	                } else {
	                    throw new \Exception("No field $targetField for $key on " . get_class($this));
	                }
	            }
	        }
	    }
        elseif ($val OR in_array($key, $md->getFieldNames())) {
            $_val = $val ? \Dope\Doctrine::getRepository($mapping['targetEntity'])->find($val) : null;
            $this->__set($key, $_val);
        }
    }
	
	public function unlinkFromArray(array $array)
	{
		$md = \Dope\Doctrine::getEntityManager()->getClassMetadata(get_class($this));
		
		$keys = array_merge(
		        $md->getFieldNames(),
		        $md->getAssociationNames()
		);
		
		foreach ($keys as $key) {
		    $key = rtrim($key, 's');
		    $keyPlural = $key . 's';
		    $val = isset($array[$key]) ? $array[$key] : null;
		    $valKeyPlural = isset($array[$keyPlural]) ? $array[$keyPlural] : $val;
		    
		    if (!isset($array[$key]) AND !isset($array[$keyPlural])) {
		        continue;
		    }
				
			if ($md->hasAssociation($key) AND $val) {
				$this->$key = null;
			}
			elseif ($md->hasAssociation($keyPlural) AND $valKeyPlural) {
				/*
				 * We don't know which side is owning side,
				 * so we save on both.
				 * @todo Figure out if we can simplify this.
				 */
				
				$record = \Dope\Doctrine::getRepository($md->getAssociationTargetClass($keyPlural))
					->find($valKeyPlural);
				
				/* Remove our side */
				$this->$keyPlural->removeElement($record);
				
				/* Save other side */
				$targetField = $md->getAssociationMappedByTargetField($keyPlural);
				$record->{$targetField}->removeElement($this);
			}
		}
		
		$this->save();
		
		return true;
	}
	
	public function getPrinterTemplatePath()
	{
		return join('/', array(
			Config::getOption('file.mailmerge.path'), 
			$this->getEntityKey() . '.docx'
		));
	}
	
	public function hasPrinterTemplate()
	{
		return file_exists($this->getPrinterTemplatePath());
	}
	
	public function getPrinterTemplate()
	{
		return new Printer\Template(
			$this->getPrinterTemplatePath(),
			$this
		);
	}
	
	/** @ORM\postLoad */
	public function postLoad() {}
	
	/** @ORM\prePersist */
	public function prePersist() {}
	
	/** @ORM\postPersist */
	public function postPersist() {}
	
	/** @ORM\preUpdate */
	public function preUpdate() {}
	
	/** @ORM\postUpdate */
	public function postUpdate() {}
}