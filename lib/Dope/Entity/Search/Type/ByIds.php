<?php

namespace Dope\Entity\Search\Type;

use Dope\Entity\Search;

class ByIds extends Plain
{
	protected $ids = array();
	protected $total = 0;
	
	public function __construct(array $ids, $total)
	{
		$this->ids = $ids;
		$this->total = $total;
	}
	
	public function preExecute()
	{	
		$this->getSearch()->useLimit(false);
		
		parent::preExecute();
	}
	
	public function postExecute()
	{
		$this->getSearch()->getQueryBuilder()->add('where',
		    $this->getSearch()->getQueryBuilder()->expr()->in($this->getSearch()->getTableAlias() . '.id', $this->ids)
		);
		
		parent::postExecute();
		
		$this->getSearch()->setCount($this->total);
	}
}