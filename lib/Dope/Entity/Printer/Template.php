<?php

namespace Dope\Entity\Printer;

use Dope\Entity;

class Template extends \Dope\Printer\Template
{
	public function __construct($path, Entity $entity=null)
	{
		parent::__construct($path);
		
		if ($entity) {
			$this->assignFromEntity($entity);
		}
	}
	
	public function assignFromEntity(Entity $entity)
	{
		foreach ($entity as $key => $val) {
			$this->assign($key, $val);
		}
	}
}