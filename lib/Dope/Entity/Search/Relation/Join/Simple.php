<?php

namespace Dope\Entity\Search\Relation\Join;

use Doctrine\ORM\QueryBuilder;

class Simple extends \Dope\Entity\Search\Relation\Join
{
	public function processSelectPre(QueryBuilder $select)
	{
		return $select;
	}
	
	public function processSelect(QueryBuilder $select)
	{
		$select->innerJoin(
			$this->getRelation()->mapping['targetEntity'],
			$this->getRelation()->getTableAlias()
		);
		
		return $select;
	}
	
	public function processSelectPost(QueryBuilder $select)
	{
		return $select;
	}
}