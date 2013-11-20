<?php

namespace Dope\Doctrine\Common;

use Doctrine\Common\EventArgs;

class EventManager extends \Doctrine\Common\EventManager
{
	/**
	 * Debug object
	 *
	 * @var \Dope\Debug
	 */
	protected $debug;
	
	/**
	 *
	 * @return \Dope\Debug
	 */
	public function getDebug()
	{
		if (! $this->debug instanceof \Dope\Debug) {
			$this->debug = new \Dope\Debug();
		}
	
		return $this->debug;
	}
	
	/**
	 * Dispatches an event to all registered listeners.
	 *
	 * @param string $eventName The name of the event to dispatch. The name of the event is
	 *                          the name of the method that is invoked on listeners.
	 * @param EventArgs $eventArgs The event arguments to pass to the event handlers/listeners.
	 *                             If not supplied, the single empty EventArgs instance is used.
	 * @return boolean
	 */
	public function dispatchEvent($eventName, EventArgs $eventArgs = null)
	{
		$listeners = $this->getListeners();
	
		if (isset($listeners[$eventName])) {
			$eventArgs = $eventArgs === null ? EventArgs::getEmptyInstance() : $eventArgs;
			$this->getDebug()->punch(__CLASS__, __LINE__, $eventName);
	
			foreach ($listeners[$eventName] as $listener) {
				$listener->$eventName($eventArgs);
				$this->getDebug()->punch(__CLASS__, __LINE__, get_class($listener));
			}
		}
	}
}