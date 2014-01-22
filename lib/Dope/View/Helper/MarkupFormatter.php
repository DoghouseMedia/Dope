<?php

class Dope_View_Helper_MarkupFormatter extends Zend_View_Helper_Abstract
{
	protected $_number;
	
    /**
     * Return formatted date
     * 
     * @return int
     */
	public function markupFormatter($number)
	{
		$this->_number = $number;
		return $this;
	}

	public function __toString()
	{
		return ($this->_number * 100) . '%';
	}
}
