<?php

class Dope_View_Helper_FieldLimiter extends Zend_View_Helper_Abstract
{   
	public function fieldLimiter($string, $length=30)
	{
		$strLen = strlen($string);
		
		if ($strLen > $length) {
			$string = substr($string, 0, $length) . '...';
		}
		
		return $string;
	}
}
