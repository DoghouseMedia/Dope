<?php

namespace Dope\Entity\Search\Relation;

use Dope\Entity\Search,
	Doctrine\ORM\QueryBuilder;

abstract class Join
{
	/**
	 * @var \Dope\Entity\Search\Relation
	 */
	protected $relation;
	
	/**
	 * @var string
	 */
	protected $value;
	
	/**
	 * @var array
	 */
	protected $joins = array();
	
	/**
	 * Constructor
	 * 
	 * @param \Dope\Entity\Search\Relation $relation
	 * @param string $value
	 */
	public function __construct(Search\Relation $relation, $value)
	{
		$this->relation = $relation;
		
		/* If it's a plain/simple filter. Convert it. */
		$this->value = is_array($value) ?
			$value :
			array('has:or:' . $value);
	}
	
	public function logJoin($tableName, $tableAlias)
	{
		if (! isset($this->joins[$tableName])) {
			$this->joins[$tableName] = array();
		}
		$this->joins[$tableName][] = $tableAlias;
	}
	
	public function getJoins($tableName=null)
	{
		if ($tableName) {
			if (isset($this->joins[$tableName])) {
				return $this->joins[$tableName];
			} else {
				return false;
			}
		} else {
			return $this->joins;
		}
	}
	
	/**
	 * @return \Dope\Entity\Search\Relation
	 */
	public function getRelation()
	{
		return $this->relation;
	}
	
	/**
	 * @return Doctrine_Query
	 */
	abstract public function processSelectPre(QueryBuilder $select);
	
	/**
	 * @return Doctrine_Query
	 */
	abstract public function processSelect(QueryBuilder $select);
	
	/**
	 * @return Doctrine_Query
	 */
	abstract public function processSelectPost(QueryBuilder $select);
}