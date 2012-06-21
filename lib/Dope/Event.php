<?php

namespace Dope;

class Event
{
	/**
	 * @param \Dope\Event\Args $args
	 */
	public function __construct(Event\Args $args=null)
	{
		\Dope\Doctrine::getEventManager()->addEventListener(static::event, $this);
		\Dope\Doctrine::getEventManager()->dispatchEvent(static::event, $args);
	}
}