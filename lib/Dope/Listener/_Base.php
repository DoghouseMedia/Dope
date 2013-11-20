<?php

namespace Dope\Listener;

use Doctrine\ORM\Events,
	Dope\Doctrine;

class _Base {
	public function __construct()
	{
		Doctrine::getEventManager()->addEventListener(
			array(Events::onFlush),
			$this
		);
	}
}