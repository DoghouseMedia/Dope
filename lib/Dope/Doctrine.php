<?php

namespace Dope;

use \Doctrine\Common\EventManager;

class Doctrine
{
	static $eventManager;

	/**
	 * Is flushing?
	 *
	 * This is set to true during a flush to avoid
	 * a loop when other entities call save() during
	 * the *Flush() lifecycle events.
	 *
	 * @var bool $isFlushing
	 */
	protected static $isFlushing = false;
	
	public static function getInstance()
	{
		return \Zend_Registry::get('doctrine');
	}
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public static function getEntityManager()
	{
		return static::getInstance()->getEntityManager();
	}
	
	/**
	 * Gets the repository for an entity class.
	 *
	 * @param string $entityName The name of the entity.
	 * @return \Dope\Doctrine\ORM\EntityRepository The repository class.
	 */
	public static function getRepository($entityName)
	{
		return static::getEntityManager()->getRepository($entityName);
	}
	
	/**
	 * @return \Doctrine\Common\EventManager
	 */
	public static function getEventManager()
	{
		if (! static::$eventManager instanceof EventManager) {
			static::$eventManager = new EventManager();
		}
		
		return static::$eventManager;
	}
	
	public static function isFlushing($isFlushing = null)
	{
		if (is_bool($isFlushing)) {
			static::$isFlushing = $isFlushing;
		}
		
		return (bool) static::$isFlushing;
	}
	
	public static function flush($entity = null)
	{
		$em = static::getEntityManager();
		
		/* Flush or compute */
		if (static::isFlushing()) {
			if ($entity) {
				/* Compute Changes */
				$em->getUnitOfWork()->recomputeSingleEntityChangeSet(
					$em->getClassMetadata(get_class($entity)),
					$entity
				);
				$em->getUnitOfWork()->computeChangeSet(
					$em->getClassMetadata(get_class($entity)),
					$entity
				);
			}
		}
		else {
			static::isFlushing(true);
			/* Flush */
			$em->flush();
			static::isFlushing(false);
		}
	}
}