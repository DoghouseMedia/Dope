<?php

namespace Dope;

class ArrayObject extends \ArrayObject
{
	public function __get($key)
	{
		return $this->offsetGet($key);
	}
}