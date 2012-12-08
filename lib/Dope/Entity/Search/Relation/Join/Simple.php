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
			(string) $this->getRelation()->mapping['targetEntity'],
			(string) $this->getRelation()->getTableAlias()
		);
		
		return $select;
	}
	
	public function processSelectPost(QueryBuilder $select)
	{
		return $select;
	}
}