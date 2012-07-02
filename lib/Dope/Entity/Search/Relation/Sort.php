<?php

namespace Dope\Entity\Search\Relation;

use Dope\Entity\Search,
	Doctrine\ORM\QueryBuilder;

class Sort
{
	protected $relation;
	
	public function __construct(Search\Relation $relation)
	{
		$this->relation = $relation;
	}
	
	/**
	 * @return \Dope\Entity\Search\Relation
	 */
	public function getRelation()
	{
		return $this->relation;
	}
	
	public function processSelectPre(QueryBuilder $select)
	{
		return $select;
	}
	
	public function processSelect(QueryBuilder $select)
	{
// 		if(! $this->getRelation()->hasJoin()) {
// 			$select->innerJoin(
// 				$this->getRelation()->getTableName() . ' ' . $this->getRelation()->getTableAlias()
// 			);
// 			$this->getRelation()->getTableAlias()->isUsed(true);
// 		}
			
// 		$toStringColumnNames = Doctrine_Core::getTable($this->getRelation()->getClassName())->getToStringColumnNames();
			
// 		$_tableAliasesSelectParts = '';
// 		foreach($this->getRelation()->getSearch()->getEntityRepository()->getTableAliases()->getUsed() as $__tableAlias) {
// 			$_tableAliasesSelectParts .= $__tableAlias . '.id,' . $__tableAlias . '.' . $toStringColumnNames[0] . ',';
// 		}
// 		rtrim($_tableAliasesSelectParts, ',');
		
// 		$select->select( $this->getRelation()->getSearch()->getData()->select ?
// 			$this->getRelation()->getSearch()->getData()->select . ',' . $_tableAliasesSelectParts :
// 			$this->getRelation()->getTableAlias() . '.*,' . $_tableAliasesSelectParts
// 		);
		
// 		$select->orderBy(
// 			$this->getRelation()->getTableAlias() . '.'
// 			. $toStringColumnNames[0] . ' '
// 			. $this->getRelation()->getSearch()->getSort()->getDir()
// 		);
		
		return $select;
	}
	
	public function processSelectPost(QueryBuilder $select)
	{
		return $select;
	}
}