<?php

namespace Dope\Form\Entity\Search;

class Filter
{
	protected $key;
	protected $title;
	protected $type;
    protected $sort;
	protected $options;
	
	public function __construct($key, $title, $type=null, $sort=null, array $options=null)
	{
		$this->key = $key;
		$this->title = $title;
		$this->type = $type;
        $this->sort = $sort;
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

    public function getSort()
    {
        return $this->sort;
    }
	
	public function getOptions()
	{
		return $this->options;
	}
}