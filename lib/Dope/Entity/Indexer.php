<?php

namespace Dope\Entity;

use Dope\Entity,
	Dope\Doctrine,
	Doctrine\ORM\Proxy\Proxy,
	Dope\Entity\Index\Analyzer;

class Indexer
{
	/**
	 * @var \Dope\Entity
	 */
	protected $entity;
	
	/**
	 * @var \Dope\Entity
	 */
	protected $targetEntity;
	
	/**
	 * @param \Dope\Entity $entity
	 */
	public function __construct(Entity $entity)
	{
		$this->entity = $entity;
		
		$indexAnnotation = $this->entity->getDefinition()->getIndexAnnotation();
		$this->indexRepository = Doctrine::getRepository($indexAnnotation->entity);
		$this->targetEntity = $indexAnnotation->targetEntity
			? $this->entity->{$indexAnnotation->targetEntity}
			: $this->entity;
			
		if ($this->targetEntity instanceof Proxy) {
			$this->targetEntity->__load();
		}
		
		return $this;
	}
	
	public function add($fieldName, $value)
	{
		$storageFieldName = ($this->targetEntity == $this->entity)
			? $fieldName
			: '_' . $this->entity->getEntityKey() . '_' . $this->entity->id;
		
		/* Analyze content */
		$terms = Analyzer::analyze($value);
		
		/* Save index */
		foreach ($terms as $pos => $term) {
			$indexEntry = $this->indexRepository->newInstance();
			$indexEntry->keyword = $term;
			$indexEntry->position = $pos;
			$indexEntry->id = $this->targetEntity->id;
			$indexEntry->field = $storageFieldName;
			
			Doctrine::getEntityManager()->persist($indexEntry);
			Doctrine::flush($indexEntry);
		}
	}
	
	public function remove($fieldName=null)
	{
		$findByConditions = array(
			'id' => $this->targetEntity->id
		);
		
		if ($fieldName) {
			$findByConditions['field'] = ($this->targetEntity == $this->entity)
				? $fieldName
				: '_' . $this->entity->getEntityKey() . '_' . $this->entity->id;
		}
		
		$entities = $this->indexRepository->findBy($findByConditions);
			
		foreach ($entities as $entity) {
			Doctrine::getEntityManager()->remove($entity);
		}
		Doctrine::flush();
	}
}