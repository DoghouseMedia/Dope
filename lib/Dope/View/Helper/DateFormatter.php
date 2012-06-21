<?php

use \Dope\Entity\Helper\Date as DateHelper;

class Dope_View_Helper_DateFormatter extends Zend_View_Helper_Abstract
{
	protected $_date;
	
    /**
     * Return formatted date
     * 
     * @return int
     */
	public function dateFormatter($date)
	{
		$this->_date = $date;
		return $this;
	}

	public function date()
	{
		return DateHelper::format($this->_date);
	}
	
	public function time()
	{
		return DateHelper::formatTime($this->_date);
	}

	public function month()
	{
		if (! $this->_date) {
			return '';
		}
		
		$months = Core_Form::getMonths();
		return $months[str_pad($this->_date, 2, '0', STR_PAD_LEFT)];
	}
	
	public function iso()
	{
		return DateHelper::format($this->_date, 'Y-m-d');
	}
	
	public function __toString()
	{
		return $this->date();
	}
}
