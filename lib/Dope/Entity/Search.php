<?php

namespace Dope\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;

use 
	Dope\Doctrine\ORM\EntityRepository,
	Dope\Controller\Data,
	Dope\Doctrine,
	Dope\Log,
	Doctrine\ORM\QueryBuilder;

class Search
{
	const MODE_NORMAL = '\Dope\Entity\Search::MODE_NORMAL';
	const MODE_COUNT_ONLY = '\Dope\Entity\Search::MODE_COUNT_ONLY';
	const MODE_WITH_PAGINATION = '\Dope\Entity\Search::MODE_WITH_PAGINATION';
	
	const LIMIT_YES = true;
	const LIMIT_FALSE = false;
	
	/**
	 * @var \Dope\Doctrine\ORM\EntityRepository
	 */
	protected $entityRepository;
	
	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	protected $queryBuilder;
	
	/**
	 * @var \Dope\Controller\Data
	 */
	protected $data;
	
	/**
	 * @var \Dope\Entity\Search\Sort
	 */
	protected $sort;
	
	/**
	 * Profiler object
	 * 
	 * @var \Dope\Profiler
	 */
	protected $profiler;
	
	/**
	 * Table alias
	 * 
	 * @var string
	 */
	protected $tableAlias;
	
	/**
	 * Relations
	 */
	protected $relations;
	
	/**
	 * Delegated WHERES
	 */
	protected $delegatedWheres = array();
	
	/**
	 * Records
	 * 
	 * @var array
	 */
	protected $records = array();
	
	/**
	 * Count
	 * 
	 * @var int
	 */
	protected $count = 0;
	
	/**
	 * IDs
	 * 
	 * @var array
	 */
	protected $ids = array();
	
	/**
	 * Use Limit
	 * 
	 * @var boolean
	 */
	protected $useLimit = true;
	
	/**
	 * Mode
	 * 
	 * @var string
	 */
	protected $mode = self::MODE_NORMAL;
	
	/**
	 * Callbacks
	 * 
	 * @var array
	 */
	protected $callbacks = array();
	
	/**
	 * Type
	 * 
	 * @var \Dope\Entity\Search\Type\_Base
	 */
	protected $type;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->getProfiler()->punch('search start');
		
		/* Create SELECT query */
		$this->queryBuilder = Doctrine::getEntityManager()->createQueryBuilder();
	}
	
	/**
	 * @param EntityRepository $entityRepository
	 * @return \Dope\Entity\Search
	 */
	public function setEntityRepository(EntityRepository $entityRepository)
	{
		$this->entityRepository = $entityRepository;
		return $this;
	}
	
	/**
	 * @param Data $data
	 * @return \Dope\Entity\Search
	 */
	public function setData(Data $data)
	{
		$this->data = $data;
		return $this;
	}
	
	/**
	 * Get data
	 * 
	 * @return \Dope\Controller\Data
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * Get QueryBuilder
	 *
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilder()
	{
	    return $this->queryBuilder;
	}
	
	/**
	 * Get delegated WHERES
	 *
	 * @return array
	 */
	public function getDelegatedWheres()
	{
		return $this->delegatedWheres;
	}
	
	/**
	 * Get sort
	 * 
	 * @return \Dope\Entity\Search\Sort
	 */
	public function getSort()
	{
		if (! $this->sort instanceof Search\Sort) {
			$this->sort = new Search\Sort($this);
		}
		
		return $this->sort;
	}
	
	/**
	 * Get entity repository
	 * 
	 * @return \Dope\Doctrine\ORM\EntityRepository $entityRepository
	 */
	public function getEntityRepository()
	{
		return $this->entityRepository;
	}
	
	/**
	 * @return Core_Search_TableAlias 
	 */
	public function getTableAlias()
	{
		if (! $this->tableAlias) {
			$this->tableAlias = $this->getEntityRepository()
				->getTableAliases()
				->getNewAlias(true);
		}
		
		return $this->tableAlias;
	}
	
	public function getMode()
	{
		return $this->mode;
	}
	
	public function setMode($mode)
	{
		$this->mode = $mode;
		return $this;
	}
	
	public function useLimit($useLimit=null)
	{
	    if (is_bool($useLimit)) {
	        $this->useLimit = $useLimit;
	    }
	
	    return $this->useLimit;
	}
	
	public function getRecords()
	{
		return $this->records;
	}
	
	public function getCount()
	{
		return $this->count;
	}
	
	public function getIds()
	{
		return $this->ids;
	}
	
	public function getRangeStart()
	{
		return $this->getData()->getParam('list_start') ?: 0;
	}
	
	public function getRangeEnd()
	{
		if ($this->getData()->getParam('list_count')) {
		    return $this->getRangeStart() + min(array(
			    $this->getData()->getParam('list_count'),
			    $this->getCount()
		    ));
		}
		else {
			return $this->getRangeStart() + $this->getCount();
		}
	}
	
	public function setRecords($records)
	{
		$this->records = $records;
		return $this;
	}
	
	public function setCount($count)
	{
	    $this->count = $count;
	    return $this;
	}
	
	public function setIds($ids)
	{
	    $this->ids = $ids;
	    
	    foreach ($this->callbacks as $callback) {
	    	$callback($this->ids);
	    }
	    
	    return $this;
	}
	
	public function setType(Search\Type\_Base $type)
	{
		$this->type = $type;
		$this->type->setSearch($this);
		return $this;
	}
	
	public function onSetIds(\Closure $callback)
	{
		$this->callbacks[] = $callback;
		return $this;
	}
	
	/**
	 * Get column weight factor (for search)
	 *
	 * This should obviously be overriden when models need to specify weighting for fields
	 *
	 * @param string $columnName
	 * @param string $focusPresetName
	 * @return int column weight factor
	 */
	public function getColumnWeightFactor($columnName, $focusPresetName=false)
	{
		/**
		 * @todo Implement!!
		 */
	    return 1;
	}
	
	/**
	 * Execute
	 * 
	 * @return \Dope\Entity\Search
	 */
	public function execute()
	{
		$this->type->preExecute();
		
	    /* ----- Profiler ----- */
	    $this->getProfiler()->punch('filter start');
	
	    /* Get some variables from Doctrine */
	    $modelName = $this->getEntityRepository()->getClassName(); // eg: [App]\Entity\Candidate
	
	    /* Set FROM */
	    $this->getQueryBuilder()->from($modelName, (string) $this->getTableAlias());
	
	    /* Set distinct */
	    $this->getQueryBuilder()->distinct(true);
	
	    /* ----- Profiler ----- */
	    $this->getProfiler()->punch('filter pre where');
	
	    /* Sort - preparation */
	    $this->getSort()->useDefaultSort((bool) $this->getData()->query); // use default sort if query
	    $this->getSort()->processSelect($this->getQueryBuilder());
	
	    /* Filter */
	    $this->filter();
	
	    /* Limit */
	    $this->limit();
	
	    /* Select columns */
	    if ($this->getData()->select AND $this->getData()->select != '') {
	        $selectColumns = explode(',', $this->getData()->select);
	        foreach ($selectColumns as &$selectColumn) {
	            $selectColumn = $this->getTableAlias() . '.' . $selectColumn;
	        }
	        $this->getQueryBuilder()->select(join(',', $selectColumns));
	    }
	
	    Log::console('SELECT after filter');
	    Log::console($this->getQueryBuilder()->getDQL());
	
	    $this->type->postExecute();
	    
	    return $this;
	}
	
	/**
	 * Improve
	 * 
	 * @return \Dope\Entity\Search
	 */
	public function improve()
	{
	    $this->getProfiler()->punch('search pre populate relations');
	
	    /* Populate relations */
	    $this->populateRelations();
	
	    /* Populate toString field */
	    $this->populateToString();
	     
	    $this->getProfiler()->punch('search pre return 2');
	    //$this->debug($FINAL_DATA_ARRAY, "FINAL DATA ARRAY");
	
	    return $this;
	}
	
	public function limit()
	{
	    if ($this->useLimit()) {
	        /* Limit (and offset) */
	        if ($this->getData()->list_count) {
	            $this->getQueryBuilder()->setMaxResults(
                    (int) $this->getData()->list_count
	            );
	
	            /* Offset (for limit) */
	            if ($this->getData()->list_start) {
	                $this->getQueryBuilder()->setFirstResult(
                        (int) $this->getData()->list_start
	                );
	            }
	        }
	    }
	    
	    return $this;
	}
	
	/**
	 * Get filtered query
	 * 
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function filter()
	{
		$this->getProfiler()->punch('filter start');
		
		/*
		 * Loop over column names
		 * 
		 * - Apply sort orders
		 * - Apply where filters
		 */
		foreach ($this->getEntityRepository()->getColumnNames() as $columnName) {
			/* Apply sort */
			//$select = 
			$this->getSort()->processKeySort(
				$this->getQueryBuilder(),
				$this->getTableAlias(),
				$columnName
			);
			
			/*
			 * Filter
			 * - get value
			 * - skip if value is empty
			 * - if val is not foreign key, wrap it in '*'
			 * - add where clause
			 */
			$values = $this->getData()->getParam($columnName);

			if (! is_array($values)) {
				$values = array($values);
			}
			
			$WHERES = array(
				'AND' => array(),
				'OR' => array()		
			);
			
			foreach($values as $value) {
				$value = rtrim($value, '/');
				if ($value == '') continue;
				
				/*
				 * We do this so we can use '=' when the query has no "*".
	 			 * Else, we have to use 'LIKE'.
	 			 * We also replace "*" with the proper SQL "%".
	 			 * 
	 			 * And we check for filters (:)
				 */
				if (strpos($value, '*') === false) {
					/*
					 * Filters will submit a value that looks like of one these:
					 * 
					 * has:and:value
					 * has:or:value
					 * hasnot:and:value
					 * 
					 * We check for two semi-colons to determine whether this value is a filter or not.
					 */
					if (substr_count($value, ':') != 2) {
						// not a filter
						$opSign = true;
						$joinBool = 'OR';
						$searchOperator = $opSign ? '=' : '!=';
						$value = "'" . $value . "'";
					} else {
						// this is a filter
						list($opSign, $joinBool, $value) = explode(':', $value);
						$opSign = (bool) ($opSign=='has');
						$joinBool = strtoupper($joinBool);
						
						if (strpos($value, ',')) {
							$searchOperator = $opSign ? 'IN' : 'NOT IN';
							$value = "('" . join("','", explode(',', $value)) . "')";
						} else {
							$searchOperator = $opSign ? '=' : '!=';
							$value = "'" . $value . "'";
						}
					}
				}
				else {
					$joinBool = 'AND';
					$value = str_replace('*', '%', $value);
					$searchOperator = $opSign ? 'LIKE' : 'NOT LIKE';
				}
				
				$WHERES[$joinBool][] =	$this->getTableAlias() . '.' . $columnName . ' ' 
					. $searchOperator . ' ' . (string) $value;
			}
			
			if (count($WHERES['AND'])) {
				$this->getQueryBuilder()->andWhere(join(' AND ', $WHERES['AND']));
			}
			if (count($WHERES['OR'])) {
				$this->getQueryBuilder()->andWhere(join(' OR ', $WHERES['OR']));
			}
		}
		
		/*
		 * Loop over relations
		 * 
		 * - Apply where filters
		 * - Apply sort orders
		 * - Apply joins
		 * - Apply filters
		 */
		$this->ormRelationsCallbackProcessSelect('pre');
		$this->ormRelationsCallbackProcessSelect();
		$this->ormRelationsCallbackProcessSelect('post');
		
		$this->getProfiler()->punch('filter pre limit');
		
		//echo $select->getQuery()->getSQL(); die;
		
		return $this;
	}
	
	/**
	 * This code is stolen/duplicated from Dope\Entity::__toString().
	 * @todo Clean up
	 *
	 * @param array $collection
	 * @return array $collection
	 */
	public function populateToString()
	{	
	    $definition = new Definition($this->getEntityRepository()->getClassName());
	
	    if (count($definition->getToStringColumnNames())) {
	        foreach ($this->records as &$record) {
	            $values = array();
	            foreach ($definition->getToStringColumnNames() as $columnName) {
	                if (isset($record[$columnName])) {
	                    $values[] = $record[$columnName];
	                }
	            }
	            $record['__toString'] = (string) join(' ', $values);
	        }
	    }
	    else {
	        foreach ($this->records as &$record) {
	            $record['__toString'] = ucfirst($this->getEntityRepository()->getModelKey()) . ' ' . $record['id'];
	        }
	    }
	
	    return $this;
	}
	
	public function populateRelations()
	{
	    $md = $this->getEntityRepository()->getClassMetadata();
	    
	    foreach ($this->records as &$record) {
	        foreach ($md->getAssociationMappings() as $alias => $mapping) {	
	            Log::console($alias);
	            Log::console($mapping);
	            
	            $targetRepo = Doctrine::getRepository($mapping['targetEntity']);

	            switch ($mapping['type']) {
	                case ClassMetadata::ONE_TO_ONE:
	                case ClassMetadata::MANY_TO_ONE:
	                    if (isset($mapping['joinColumns'][0]['name']) AND
	                    	isset($record[$mapping['joinColumns'][0]['name']])
	                    ) {
	                        $record[$alias] = (string) $targetRepo->find(
	                        	$record[$mapping['joinColumns'][0]['name']]
	                        );
	                    } else {
	                        $record[$alias] = '';
	                    }
	                    break;
	
	                case ClassMetadata::ONE_TO_MANY:
	                //case ClassMetadata::MANY_TO_MANY:
	                    $record[$alias] = array();
	                    	
	                    $_entities = $targetRepo->findBy(array(
	                        $mapping['mappedBy'] => $record['id']
	                    ));
	
	                    foreach ($_entities as $_entity) {
	                        $record[$alias][] = $_entity->id;
	                    }
	                    break;
	            }
		    }
		}
	
		return $this;
	}
	
	protected function ormRelationsCallbackProcessSelect($step='')
	{
		$methodName = 'processSelect' . ucfirst($step);
		
		return $this->ormRelationsCallback($methodName,
			function (Search\Relation $relation, $methodName, QueryBuilder $select) {
				return $relation->$methodName($select);
			}
		);
	}
	
	protected function ormRelationsCallback($methodName, \Closure $callback)
	{
		foreach ($this->getRelations() as $relationName => $relation) {
			$callback($relation, $methodName, $this->getQueryBuilder());
		}

		return $this;
	}
	
	protected function getRelations()
	{
		if (! is_array($this->relations)) {
			$this->relations = array();
			foreach ($this->getEntityRepository()->getAssociationMappings() as $alias => $mapping) {
				$this->relations[$alias] = new Search\Relation($this, $mapping);
			}
		}
		
		return $this->relations;
	}
	
	public static function getArrayFromColumn($columnName, array $rows)
	{
	    $result = array();
	
	    foreach ($rows as $row) {
	        $result[] = $row[$columnName];
	    }
	
	    return $result;
	}
	
	/**
	 * @return \Dope\Profiler
	 */
	public function getProfiler()
	{
	    if (! $this->profiler instanceof \Dope\Profiler) {
	        $this->profiler = new \Dope\Profiler();
	    }
	
	    return $this->profiler;
	}
	
	public function debug($object, $title=null)
	{
	    /*
	     * @todo Encapsulate this in \Dope\Env
	     */
	     
	    // 		$showDebug = (
	    // 			\APPLICATION_ENV != '' AND
	    // 			!in_array(\APPLICATION_ENV, array('staging','development','production'))
	    // 		);
	
	    // 		if ($showDebug) {
	    // 			if (! $this->logger instanceof \Zend_Log) {
	    // 				$this->logger = new \Zend_Log();
	    //         		$this->logger->addWriter(new \Zend_Log_Writer_Firebug());
	    // 			}
	    	
	    // 			if ($title) {
	    // 				$this->logger->log('----- ' . $title . ' -----', \Zend_Log::INFO);
	    // 			}
	    	
	    // 			$this->logger->log(
	    // 				is_string($object) ? addslashes($object) : $object,
	    // 				\Zend_Log::INFO
	    // 			);
	    	
	    // 			return true;
	    // 		}
	
	    // 		return false;
	}
}
