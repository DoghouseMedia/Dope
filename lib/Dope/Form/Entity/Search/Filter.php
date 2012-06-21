<?php

namespace Dope\Form\Entity\Search;

class Filter
{
	protected $key;
	protected $title;
	protected $type;
	protected $options;
	
	public function __construct($key, $title, $type=null, array $options=null)
	{
		$this->key = $key;
		$this->title = $title;
		$this->type = $type;
		$this->options = $options;
	}
	
	public function getKey()
	{
		return $this->key;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function getOptions()
	{
		return $this->options;
	}
}