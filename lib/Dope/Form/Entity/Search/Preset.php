<?php

namespace Dope\Form\Entity\Search;

abstract class Preset
{
	abstract public function getFactors();
	
	public function getFactor($name)
	{
		$factors = $this->getFactors();
		
		return isset($factors[$name]) ?
			$factors[$name]:
			false;
	}
	
	public function getTitle()
	{	
		/* Return last part after last "\" in the class name */
		return end(
			explode('\\', get_class($this))
		);
	}
	
	public function getKey()
	{
		return strtolower($this->getTitle());
	}
}