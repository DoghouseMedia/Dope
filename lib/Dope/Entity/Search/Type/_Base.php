<?php

namespace Dope\Entity\Search\Type;

use \Dope\Entity\Search;

abstract class _Base
{
	/**
	 * Search
	 * @var \Dope\Entity\Search
	 */
	protected $search;
	
	/**
	 * @param \Dope\Entity\Search $search
	 * @return \Dope\Entity\Search\Type\_Base
	 */
	public function setSearch(Search $search)
	{
		$this->search = $search;
		return $this;
	}
	
	/**
	 * @return \Dope\Entity\Search
	 */
	protected function getSearch()
	{
		return $this->search;
	}
	
	public function getDebug()
	{
		return $this->getSearch()->getDebug();
	}
	
	abstract public function preExecute();
	abstract public function postExecute();
}