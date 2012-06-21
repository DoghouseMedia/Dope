<?php

namespace Dope\Entity\Search;

use Dope\Entity\Search;

class Filter
{
	/**
	 * @var \Dope\Entity\Search\Relation
	 */
	protected $relation;
	
	protected $tableName;
	protected $tableAlias;
	protected $signOperator;
	protected $boolOperator;
	protected $numConditions = 0;
	
	protected $_opSign;
	protected $_opBool;
	protected $_opIdString;
	
	public function __construct(Search\Relation $relation, $filterDataString)
	{
		$this->relation = $relation;

		if (strpos($filterDataString, ':') !== false) {
			list($_opSign, $_opBool, $_opIdString) = explode(':', $filterDataString);
			$this->_opSign = $_opSign;
			$this->_opBool = $_opBool;
			$this->_opIdString = $_opIdString;	
		} else {
			$this->_opSign = 'has';
			$this->_opBool = 'OR';
			$this->_opIdString = $filterDataString;
		}
		
		$this->signOperator = ($this->_opSign=='has') ? '=' : '!=';
		$this->boolOperator = (strtoupper($this->_opBool)=='AND') ? 'AND' : 'OR';
	}
	
	/**
	 * @return \Dope\Entity\Search\Relation
	 */
	public function getRelation()
	{
		return $this->relation;
	}
	
	public function getIds()
	{
		return explode(',', $this->_opIdString);
	}
	
	public function hasOr()
	{
		return (bool) (strpos($this->_opIdString, '|') !== false);
	}
	
	public function isOr($id)
	{
		return (bool) ($id[0] == '|');
	}
	
	public function getTableName()
	{
		return $this->tableName;
	}
	
	public function getTableAlias()
	{
		return $this->tableAlias;
	}
	
	public function getSignOperator()
	{
		return $this->signOperator;
	}
	
	public function getBoolOperator()
	{
		return $this->boolOperator;
	}
}