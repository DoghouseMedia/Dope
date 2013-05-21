<?php

namespace Dope;

class Debug
{
	protected $punches;
	protected $data;
	
	public function __construct()
	{
		$this->punches = new ArrayObject();
		$this->data = new ArrayObject();
	}
	
	public function punch($class, $line, $comment=false)
	{
		$this->punches[] = new ArrayObject(array(
			'class' => $class,
			'line' => $line,
			'microtime' => microtime(true),
			'comment' => $comment
		));
		
		return $this;
	}
	
	public function log($name, $data)
	{
		$this->data[] = new ArrayObject(array(
			'name' => $name,
			'data' => $data
		));
		
		return $this;
	}
	
	public function getPunches()
	{
		return $this->punches;
	}
	
	public function getData()
	{
		return $this->data;
	}
}