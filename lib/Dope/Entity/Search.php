<?php

namespace Dope\Entity;

use 
	Dope\Doctrine\ORM\EntityRepository,
	Dope\Controller\Data,
	Doctrine\ORM\QueryBuilder;

class Search
{
	const SEARCH_NORMAL = '\Dope\Entity\Search::SEARCH_NORMAL';
	const SEARCH_COUNT_ONLY = '\Dope\Entity\Search::SEARCH_COUNT_ONLY';
	const SEARCH_WITH_PAGINATION = '\Dope\Entity\Search::SEARCH_WITH_PAGINATION';
	
	/**
	 * @var \Dope\Doctrine\ORM\EntityRepository
	 */
	protected $entityRepository;
	
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
	 * Constructor
	 * 
	 * @param \Dope\Doctrine\ORM\EntityRepository $entityRepository
	 * @param \Dope\Controller\Data $data
	 */
	public function __construct(EntityRepository $entityRepository, Data $data)
	{
		$this->entityRepository = $entityRepository;
		$this->data = $data;
		
		//$this->getProfiler()->punch('search start');
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
	
	/**
	 * Get filtered query
	 * 
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function filter($useLimit=true, $useDefaultSort=true)
	{
		$this->getProfiler()->punch('filter start');
		
		/* Get some variables from Doctrine */
		$modelName = $this->getEntityRepository()->getClassName(); // eg: [App]\Entity\Candidate
		
		/* Create SELECT query */
		$select = \Dope\Doctrine::getEntityManager()->createQueryBuilder();
		
		/* Set FROM */
		$select->from($modelName, (string) $this->getTableAlias());
		
		/* Set distinct */
		$select->distinct(true);
		
		/* Select columns */
		if ($this->getData()->select AND $this->getData()->select != '') {
			$selectColumns = explode(',', $this->getData()->select);
			foreach($selectColumns as &$selectColumn) {
				$selectColumn = $this->getTableAlias() . '.' . $selectColumn;
			}
			$select->select(join(',', $selectColumns));
		}
		
		$this->getProfiler()->punch('filter pre where');
		
		/* Sort - preparation */
		$this->getSort()->useDefaultSort($useDefaultSort);
		$select = $this->getSort()->processSelect($select);
		
		/*
		 * Loop over column names
		 * 
		 * - Apply sort orders
		 * - Apply where filters
		 */
		foreach($this->getEntityRepository()->getColumnNames() as $columnName) {
			/* Normalize */
			$isFk = (bool) (substr($columnName,0,3) == 'fk_');
			
			$columnKey = $isFk ?
				preg_replace('/^fk_(.*)_id$/mis', "$1", $columnName) : 
				$columnName;
			
			/* Apply sort */
			$select = $this->getSort()->processKeySort(
				$select,
				$this->getTableAlias(),
				$columnKey
			);
			
			/*
			 * Filter
			 * - get value
			 * - skip if value is empty
			 * - if val is not foreign key, wrap it in '*'
			 * - add where clause
			 */
			$values = $this->getData()->getParam($columnKey);

			if (! is_array($values)) {
				$values = array($values);
			}
			
			$WHERES = array(
				'AND' => array(),
				'OR' => array()		
			);
			
			foreach($values as $value) {
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
			
			if ($columnName!='id' AND ($relation = $this->columnHasOtherRelation($columnName))) {
				$this->delegatedWheres[$relation->getAlias()] = $WHERES;
			}
			else {
				if (count($WHERES['AND'])) {
					$select->andWhere(join(' AND ', $WHERES['AND']));
				}
				if (count($WHERES['OR'])) {
					$select->andWhere(join(' OR ', $WHERES['OR']));
				}
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
		$this->ormRelationsCallbackProcessSelect($select, 'pre');
		$this->ormRelationsCallbackProcessSelect($select);
		$this->ormRelationsCallbackProcessSelect($select, 'post');
		
		$this->getProfiler()->punch('filter pre limit');
		
		/* Apply limit */
		if ($useLimit) {
			$select = $this->limit(
				$select,
				$this->getData()->list_count,
				$this->getData()->list_start
			);
		}
		
		return $select;
	}
	
	public function columnHasOtherRelation($columnName)
	{
// 		foreach($this->getRelations() as $relation) {
// 			if ($relation->matchesColumn($columnName) AND $relation->getType() != 'Doctrine_Relation_LocalKey') {
// 				return $relation;
// 			}
// 		}
		
		return false;
	}
	
	protected function ormRelationsCallbackProcessSelect(QueryBuilder $select, $step='')
	{
		$methodName = 'processSelect' . ucfirst($step);
		
		return $this->ormRelationsCallback($select, $methodName,
			function(Search\Relation $relation, $methodName, QueryBuilder $select) {
				return $relation->$methodName($select);
			}
		);
	}
	
	protected function ormRelationsCallback(QueryBuilder $select, $methodName, \Closure $callback)
	{
		foreach($this->getRelations() as $relationName => $relation) {
			$select = $callback($relation, $methodName, $select);
		}

		return $select;
	}
	
	protected function getRelations()
	{
		if (! is_array($this->relations)) {
			foreach($this->getEntityRepository()->getAssociationMappings() as $alias => $mapping) {
				$this->relations[$alias] = new Search\Relation($this, $mapping);
			}
		}
		
		return $this->relations;
	}
	
	public static function limit(QueryBuilder $select, $count=false, $start=0)
	{
		/* Limit (and offset) */
		if ($count) {
			$select->setMaxResults((int) $count);
			
			/* Offset (for limit) */
			if ($start) {
				$select->setFirstResult((int) $start);
			}
		}
		
		return $select;
	}
}
