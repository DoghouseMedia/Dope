<?php

namespace Dope;

class Profiler extends \ArrayObject
{
	protected $punchIndex = 0;
	
	public function punch($name)
	{
		$name = $this->punchIndex . ' :: ' . $name;
		
		$this->punchIndex++;
		
		return $this->offsetSet($name, microtime(true));
	}
}