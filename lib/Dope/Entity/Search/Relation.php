<?php

namespace Dope\Entity\Search;

use Dope\Entity\Search,
	Dope\Controller\Data,
	Doctrine\ORM\QueryBuilder;

class Relation
{
	public $mapping = array();
	
	protected $join;
	protected $sort;
	protected $search;
	
	protected $tableAlias;
	
	protected $delegatedWheres = array(
		'AND' => array(),
		'OR' => array()
	); 
	
	public function __construct(Search $search, array $mapping)
	{
		$this->search = $search;
		$this->mapping = $mapping;
		
		$this->init();
	}
	
	public function getSearch()
	{
		return $this->search;
	}
	
	public function matchesColumn($columnName)
	{
		if (! isset($this->mapping['joinColumns'])) {
			return false;
		}
		
		$isKey = in_array($columnName, array(
			$this->mapping['joinColumns'][0]['name'],
			$this->mapping['joinColumns'][0]['referencedColumnName']
		));
		
		return (bool) $isKey;
	}
	
	public function getDelegatedWheres()
	{
		return $this->delegatedWheres;
	}
	
	protected function init()
	{
		/*
		 * Try different keys
		 * 
		 * We have to do this since our naming is still not perfectly normalized.
		 * 
		 * Sometimes, the given relation name could be "categories" when we want "category",
		 * or "user" when we want "users", etc...
		 */
		$keys = array(
			$this->mapping['fieldName'],
			ucfirst($this->mapping['fieldName']),
			$this->mapping['fieldName'] . 's',
			ucfirst($this->mapping['fieldName']) . 's',
			substr($this->mapping['fieldName'],0,-3) . 'y',
			ucfirst(substr($this->mapping['fieldName'],0,-3)) . 'y'
		);
		
		/*
		 * We loop over the possible keys until we find one that suits
		 * and define some settings.
		 * 
		 * @todo In the future we should switch the class responsible for searching using these.
		 */
		foreach($keys as $key) {
			if ($this->getSearch()->getSort()->compareKey($key)) {
				$this->sort = new Relation\Sort($this);
			}
			
			$dataVal = $this->getDataVal($key);
			
			if ($dataVal) {
				$this->join = new Relation\Join\WithId($this, $dataVal);
				break;
			}
			elseif (in_array($key, explode(',', $this->getSearch()->getData()->join))) {
				$this->join = new Relation\Join\Simple($this, null);
				break;
			}
		}
	}
	
	protected function getDataVal($key)
	{
		$singularParam = $this->getSearch()->getData()->getParam($key, Data::FILTER_RESERVED_PARAMS);
		$pluralParam = $this->getSearch()->getData()->getParam(rtrim($key, 's'), Data::FILTER_RESERVED_PARAMS);
		
		if ($singularParam) {
			return $singularParam;
		}
		elseif ($pluralParam) {
			return $pluralParam;
		}
		else {
			return false;
		}
	}
	
	public function processSelectPre(QueryBuilder $select)
	{
		if ($this->hasJoin()) {
			$select = $this->join->processSelectPre($select);
		}
		
		if ($this->hasSort()) {
			$select = $this->sort->processSelectPre($select);
		}
			
		return $select;
	}
	
	public function processSelect(QueryBuilder $select)
	{
		if ($this->hasJoin()) {
			$select = $this->join->processSelect($select);
		}

		$select->select($this->getSelectFields());
			
		if ($this->hasSort()) {
			$select = $this->sort->processSelect($select);
		}
			
		return $select;
	}
	
	public function processSelectPost(QueryBuilder $select)
	{
		if ($this->hasJoin()) {
			$select = $this->join->processSelectPost($select);
		}
		
		if ($this->hasSort()) {
			$select = $this->sort->processSelectPost($select);
		}
			
		return $select;
	}
	
	protected function getSelectFields()
	{
		/*
		 * For every table used (mostly joined), we want to add its "id"
		 * field to the SELECT clause unless the whole table is being queried
		 */
		
		if ($this->getSearch()->getData()->select) {
			$joinedSelects = array();
			
			foreach(explode(',', $this->getSearch()->getData()->select) as $field) {
				$joinedSelects[] = $this->getSearch()->getTableAlias() . '.' . $field;
			}
			
			foreach($this->getSearch()->getEntityRepository()->getTableAliases()->getUsed() as $tableAlias) {
				$field = $tableAlias . '.id';
				if (! in_array($field, $joinedSelects)) {
					$joinedSelects[] = $tableAlias . '.id';
				}
			}
			
			$searchSelect = join(',', $joinedSelects);
		}
		else {
			$searchSelect = (string) $this->getSearch()->getTableAlias();
		}
		
		return $searchSelect;
	}
	
	public function hasSort()
	{
		return (bool) ($this->sort instanceof Relation\Sort);
	}
	
	public function hasJoin()
	{
		return (bool) ($this->join instanceof Relation\Join);
	}
	
	public function getTableAlias()
	{
		if (! $this->tableAlias) {
			$this->tableAlias = $this->getSearch()
				->getEntityRepository()
				->getTableAliases()
				->getNewAlias(true);
		}
		
		return $this->tableAlias;
	}
}