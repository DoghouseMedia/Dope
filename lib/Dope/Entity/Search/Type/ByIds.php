<?php

namespace Dope\Entity\Search\Type;

class ByIds extends Plain
{
	protected $ids = array();
	
	public function __construct(array $ids)
	{
		$this->ids = $ids;
	}
	
	public function preExecute()
	{
		$this->getSearch()->useLimit(false);
		
		return parent::preExecute();
	}
	
	public function execute()
	{
		$this->getSearch()->getQueryBuilder()->add('where',
		    $this->getSearch()->getQueryBuilder()->expr()->in('t.id', $this->ids)
		);
		
		return parent::execute();
	}
}