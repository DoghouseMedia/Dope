<?php

namespace Dope\Doctrine\ORM\Mapping;
use \Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 * @todo Is this not deprecated?
 */
final class Index implements Annotation
{
	/** @var string */
	public $entity;
	/** @var string */
	public $targetEntity;
	/** @var array */
	public $fields = array();
}
