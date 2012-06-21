<?php

class Dope_View_Helper_CurrencyFormatter extends Zend_View_Helper_Abstract
{
	protected $_number;
	
    /**
     * Return formatted date
     * 
     * @return int
     */
	public function currencyFormatter($number)
	{
		$this->_number = $number;
		return $this;
	}

	public function __toString()
	{
		$map = array(
			'Billion' => '1000000000', // 1 billion
			'Million' => '1000000', // million
			'Thousand' => '1000' // 1 thousand
		);
		
		foreach($map as $suffix => $threshhold) {
			if ($this->_number >= $threshhold) {
				return round($this->_number/$threshhold, 2) . ' ' . $suffix;
			}
		}
		
		return (string) $this->_number;
	}
}
