<?php

namespace Dope\Entity\Printer;

use Dope\Entity,
	Doctrine\Common\Collections\Collection;

class Template extends \Dope\Printer\Template
{
	public function __construct($path, Entity $entity=null)
	{
		parent::__construct($path);
		
		if ($entity) {
			$this->assignFromEntity($entity);
		}
	}
	
	public function assignFromEntity(Entity $entity, $prefix='')
	{
		foreach ($entity as $key => $val) {
			if ($val instanceof Collection) {
				continue;
			}

			$this->assign($prefix . $key, $val);
		}

        $entity->populatePrinterTemplate($this);
	}
}