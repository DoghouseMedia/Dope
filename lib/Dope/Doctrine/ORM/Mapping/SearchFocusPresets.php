<?php

namespace Dope\Doctrine\ORM\Mapping;
use \Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class SearchFocusPresets implements Annotation
{
	/** @var array */
	public $value = array();
	
	public function getFactor($columnName, $presetName='')
	{
	    foreach ($this->value as $preset) {
	        if ($preset['name'] == $presetName) {
	            return isset($preset['factors'][$columnName])
	                ? $preset['factors'][$columnName]
	                : 1;
	        }
	    }
	    
	    return 1;
	}
}
