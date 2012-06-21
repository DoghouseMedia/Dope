<?php

namespace Dope\Entity\Search\Table;

class Alias
{
	protected $alias;
	
	protected $isUsed = false;
	
	protected static $uniqueIndex = 0;
	
	public function __construct($isUsed=null)
	{
		$this->alias = 't' . $this->getUniqueIndex();
		$this->isUsed($isUsed);
	}
	
	public function isUsed($isUsed=null)
	{
		if (is_bool($isUsed)) {
			$this->isUsed = $isUsed;
		}
		
		return (bool) $this->isUsed;
	}
	
	public function __toString()
	{
		return $this->alias;
	}
	
	protected function getUniqueIndex()
	{
		static::$uniqueIndex++;
		return static::$uniqueIndex;
	}
}