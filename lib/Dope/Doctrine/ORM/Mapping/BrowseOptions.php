<?php

namespace Dope\Doctrine\ORM\Mapping;
use \Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class BrowseOptions implements Annotation
{
	/** @var boolean */
	public $showForm = false;
	
	/**
	 * @return boolean
	 */
	public function getShowForm()
	{
		return (bool) $this->showForm;
	}
}
