<?php

namespace Dope\Doctrine\ORM\Mapping;
use \Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class SearchFilters implements Annotation
{
	/** @var array */
	public $value = array();
}
