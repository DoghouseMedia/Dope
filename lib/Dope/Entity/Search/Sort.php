<?php

namespace Dope\Entity\Search;

use Dope\Entity\Search,
	Doctrine\ORM\QueryBuilder;

class Sort
{
	/**
	 * @var \Dope\Entity\Search
	 */
	protected $search;
	
	/**
	 * @var bool
	 */
	protected $useDefaultSort = true;
	
	/**
	 * Sort key
	 * @var string|false
	 */
	protected $sortKey = false;
	
	/**
	 * Sort dir
	 * @var string|false ('ASC', 'DESC', false)
	 */
	protected $sortDir = false;
	
	/**
	 * User sort
	 * @var string|false
	 */
	protected $userSort = false;
	
	/**
	 * Constructor
	 * 
	 * @param \Dope\Entity\Search
	 */
	public function __construct(Search $search, $useDefaultSort=true)
	{
		$this->search = $search;
		$this->userSort = $this->getSearch()->getData()->sort ?: false; 
		
		$this->useDefaultSort($useDefaultSort);
		$this->configure($this->userSort);
	}
	
	/**
	 * @return \Dope\Entity\Search
	 */
	public function getSearch()
	{
		return $this->search;
	}
	
	public function getKey()
	{
		return $this->sortKey;
	}
	
	public function getDir()
	{
		return $this->sortDir;
	}
	
	protected function configure($dataSort=false, $useDefaultSort=true)
	{		
		if (strpos($dataSort, ' ')) {
			list($sortKey, $sortDir) = explode(' ', $dataSort);
			$this->sortKey = $sortKey;
			$this->sortDir = $sortDir;
		}
		elseif ($dataSort) {
			$this->sortKey = $dataSort;
			$this->sortDir = 'ASC';
		}
		elseif ($useDefaultSort) {
			$defaultSort = $this->getSearch()->getEntityRepository()->getDefaultSort();
			$this->configure($defaultSort, false);
		}
	}
	
	public function processSelect(QueryBuilder $select)
	{
		if ($this->useDefaultSort() AND $this->getSearch()->getEntityRepository()->getDefaultSort()) {
			$select->orderBy($this->getSearch()->getEntityRepository()->getDefaultSort());
		}
		
		return $select;
	}
	
	public function compareKey($key)
	{
		return (bool) (strtolower($key) == strtolower($this->getKey()));
	}
	
	public function processKeySort(QueryBuilder $select, $tableAlias, $key)
	{
		if ($this->compareKey($key)) {
			$select->orderBy(
				$tableAlias . '.' . $this->getKey(),
				$this->getDir()
			);
		}
		
		return $select;
	}
	
	public function useDefaultSort($useDefaultSort=null)
	{
		if (is_bool($useDefaultSort)) {
			$this->useDefaultSort = $useDefaultSort;
		}
		
		return $this->useDefaultSort;
	}
	
	public function getUserSort()
	{
		return $this->userSort;
	}
	
	public function hasUserSort()
	{
		return (bool) $this->getUserSort();
	}
}