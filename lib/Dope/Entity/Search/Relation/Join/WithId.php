<?php

namespace Dope\Entity\Search\Relation\Join;

use Dope\Entity\Search,
	Doctrine\ORM\QueryBuilder;

class WithId extends Search\Relation\Join
{
	protected $filters = array();
	
	public function __construct(Search\Relation $relation, $value)
	{
		if (!is_array($value)) {
			$value = array($value);
		}
		
		parent::__construct($relation, $value);
		
		$this->filters = array(
			'=' => array(
				'AND' => array(),
				'OR' => array()
			),
			'!=' => array(
				'AND' => array(),
				'OR' => array() // this does not make sense. should not be used!
			)
		);
	}
	
	public function addFilter($filterDataString)
	{
		$filter = new Search\Filter($this->getRelation(), $filterDataString);
		$this->filters[$filter->getSignOperator()][$filter->getBoolOperator()][] = $filter;
		
		return $this;
	}
	
	public function processSelectPre(QueryBuilder $select)
	{
		foreach($this->value as $filterDataString) {
			$this->addFilter($filterDataString);
		}
		
		return $select;
	}
	
	public function processSelect(QueryBuilder $select)
	{
		/*
		 * Create joins for filters that are inclusive (=)
		 */
		
		/*
		 * 
		 */
		$delegatedWheres = array_merge(
			$this->getRelation()->getSearch()->getDelegatedWheres(),
			$this->getRelation()->getDelegatedWheres()
		);
		
		/*
		 * AND
		 */
		foreach ($this->filters['=']['AND'] as $filter) {
			$WHERES = array();

			foreach ($filter->getIds() as $id) {
				$_tableAlias = $this->getRelation()->getSearch()->getEntityRepository()
					->getTableAliases()->getNewAlias(true);

				$select->innerJoin(
					(string) $this->getRelation()->getSearch()->getTableAlias()
						. '.' . $this->getRelation()->mapping['fieldName'],
					(string) $_tableAlias
				);
			
				$WHERES[] = $_tableAlias . '.id = ' . (int) $id;
			}
			
			if (isset($delegatedWheres[$this->getRelation()->mapping['fieldName']])) {
				$WHERES = array_merge($WHERES, $delegatedWheres[$this->getRelation()->mapping['fieldName']]['AND']);
			}
			
			$select->andWhere('(' . join(' AND ', $WHERES) . ')');
		}
		
		/*
		 * OR
		 */
		foreach ($this->filters['=']['OR'] as $filter) {
			$select->innerJoin(
				(string) $this->getRelation()->getSearch()->getTableAlias()
					. '.' . $this->getRelation()->mapping['fieldName'],
				(string) $this->getRelation()->getTableAlias()
			);
			
			$WHERES = array();
			
			foreach($filter->getIds() as $id) {
				$WHERES[] = $this->getRelation()->getTableAlias() . '.id = ' . (int) $id;
			}
			
			if (isset($delegatedWheres[$this->getRelation()->mapping['fieldName']])) {
				$WHERES = array_merge($WHERES, $delegatedWheres[$this->getRelation()->mapping['fieldName']]['OR']);
			}

			$select->andWhere('(' . join(' OR ', $WHERES) . ')');
		}
		
		return $select;
	}
	
	public function processSelectPost(QueryBuilder $select)
	{
		/*
		 * Create joins for filters that are exclusive (!=)
		 */
		
		/*
		 * AND
		 */
		foreach ($this->filters['!=']['AND'] as $filter) {
			$joinAliases = $this->getJoins($this->getRelation()->mapping['targetEntity']);
			
			if (!is_array($joinAliases) OR !count($joinAliases)) {
				/*
				 * Add an join
				 */	
				$select->innerJoin(
					(string) $this->getRelation()->getSearch()->getTableAlias()
						. '.' . $this->getRelation()->mapping['fieldName'],
					(string) $this->getRelation()->getTableAlias()
				);

				$joinAliases = $this->getJoins($this->getRelation()->mapping['targetEntity']);
			}
			
			/*
			 * Add a WHERE clause for each join of this tableName/tableAlias
			 * and exclude our ids
			 */	
			
			if (is_array($joinAliases)) {
				foreach($joinAliases as $joinAlias) {
					$_alias = $this->getRelation()->getSearch()->getEntityRepository()
						->getTableAliases()->getNewAlias(true);
					
					$select->andWhere(
						(string) $this->getRelation()->getSearch()->getTableAlias() . '.id NOT IN'
						. ' (SELECT ' . $_alias . '.' . $this->getRelation()->mapping['joinColumns'][0]['name'] 
						. ' FROM ' . $this->getRelation()->mapping['targetEntity'] . ' ' . $_alias
						. ' WHERE ' . $_alias . '.' . $this->getRelation()->mapping['joinColumns'][0]['referencedColumnName']
						. ' IN (' . join(',', $filter->getIds()) . '))'
					);
				}
			}
		}
		
		return $select;
	}
}