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
		if (static::isFlushing() AND $entity) {
			$metadata = $em->getClassMetadata(get_class($entity));
			
			/*
			 * Compute Changes
			 * 
			 * I wish I knew why I had to call these in this order,
			 * but different listeners break at different stages if I don't.
			 * 
			 * @todo Fix (re)computing of Doctrine Changesets (ask Doctrine if you have to)
			 */
			$em->getUnitOfWork()->computeChangeSet($metadata, $entity);
			$em->getUnitOfWork()->recomputeSingleEntityChangeSet($metadata, $entity);
			$em->getUnitOfWork()->computeChangeSet($metadata, $entity);
		}
		else {
			static::isFlushing(true);
			$em->flush();
			static::isFlushing(false);
		}
	}
}