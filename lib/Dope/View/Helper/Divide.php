<?php

class Dope_View_Helper_Divide extends Zend_View_Helper_Abstract
{
	public function divide($dividend, $divisor, $precision=2)
	{
		if ($divisor == 0) {
			return 0;
		}
		
		return round($dividend / $divisor, $precision);
	}
}
