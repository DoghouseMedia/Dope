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

            // convert datetimes to human readable
            if ($entity->{$key} instanceof \DateTime) {
                $this->assign(
                    $prefix . $key . '_formatted_short',
                    $entity->{$key}->format('d M Y')
                );
            }

			$this->assign($prefix . $key, $val);
		}

        $entity->populatePrinterTemplate($this);
	}
}