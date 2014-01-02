<?php

namespace Dope\Entity;

use Dope\Entity,
	Dope\Doctrine,
	Doctrine\ORM\Proxy\Proxy,
	Dope\Entity\Indexer\Analyzer;

class Indexer
{
    const BULK = true;
    
	/**
	 * @var \Dope\Entity
	 */
	protected $entity;
	
	protected $target;
	
	/** 
	 * @var array
	 */
	public $fields = array();
	
	/**
	 * @param \Dope\Entity $entity
	 * @param array $indexerAnnotation
	 */
	public function __construct(Entity $entity, $indexerAnnotation)
	{
		$this->entity = $entity;
		$this->indexRepository = Doctrine::getRepository($indexerAnnotation['entity']);
		$this->target = isset($indexerAnnotation['target'])
			? $this->entity->{$indexerAnnotation['target']}
			: $this->entity;
				
		if ($this->target instanceof Proxy) {
			$this->target->__load();
		}
	}

    /**
     * Gets the repository for an entity class.
     *
     * @param string $entityName The name of the entity.
     * @return \Dope\Doctrine\ORM\EntityRepository The repository class.
     */
    public function getRepository()
    {
        return $this->indexRepository;
    }
	
	public function add($fieldName, $value, $bulk=false)
	{
		/* Analyze content */
		$terms = Analyzer::analyze($value);
		
		foreach ($this->getEntriesByTarget($fieldName) as $id => $storageFieldname) {
			$this->debug("ADD " . $storageFieldname . " = " . join(',', $terms));
			
			/* Save index */
			foreach ($terms as $pos => $term) {
				$indexEntry = $this->indexRepository->newInstance();
				$indexEntry->keyword = $term;
				$indexEntry->position = $pos;
				$indexEntry->id = $id;
				$indexEntry->field = $storageFieldname;
				
				$this->debug(join(' - ', array(
					'ADD', $storageFieldname, $id, $pos, $term, get_class($indexEntry)
				)));
				
				Doctrine::getEntityManager()->persist($indexEntry);
				if (!$bulk) {
    				Doctrine::isFlushing(false);
    				Doctrine::flush($indexEntry);
				}
			}
		}
		
		return $this;
	}
	
	public function remove($fieldName=null, $bulk=false)
	{
		foreach ($this->getEntriesByTarget($fieldName) as $id => $storageFieldname) {
			$this->debug("REMOVE $fieldName ($storageFieldname) BY ID $id");
			
			$qb = $this->indexRepository->createQueryBuilder('i');
			
			$qb->where('i.id = :id');
			$qb->setParameter('id', $id);

			if ($fieldName) {
				$qb->andWhere('i.field = :field');
				$qb->setParameter('field', $storageFieldname);
			}
			else {
				$qb->andWhere('i.field LIKE :field');
                $qb->setParameter('field', addcslashes($storageFieldname, '_').'%');
			}

			$entities = $qb->getQuery()->execute();

			foreach ($entities as $entity) {
				Doctrine::getEntityManager()->remove($entity);
			}
		}

        if (!$bulk) {
            Doctrine::isFlushing(false);
            Doctrine::flush();
        }

		return $this;
	}
	
	protected function getEntriesByTarget($fieldName=null)
	{
		if ($this->target == $this->entity) {
			return array(
				$this->target->id => $fieldName
			);
		}
		
		$entityFieldname = join('', array(
			'_' . $this->entity->getEntityKey() .
			'_' . $this->entity->id .
			'_' . $fieldName
		));
		
		if ($this->target instanceof \Doctrine\ORM\PersistentCollection) {
			$entries = array();
			foreach ($this->target as $_target) {
				$entries[$_target->id] = $entityFieldname;
			}
			return $entries;
		}
		
		if ($this->target instanceof \Dope\Entity) {
			return array(
				$this->target->id => $entityFieldname
			);
		}
		
		return false;
	}
	
	protected function debug($msg) {
		//echo '[' . $this->indexRepository->getClassName() . ']' . $msg . " <br>\n";
		\Dope\Log::console('[' . $this->indexRepository->getClassName() . ']' . $msg);
	}
}