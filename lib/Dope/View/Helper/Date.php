<?php

class Dope_View_Helper_Date extends Zend_View_Helper_Abstract
{
	protected $dateString;
	
	public function date($dateString)
	{
		$this->dateString = $dateString;
		return $this;
	}
	
	public function period($dateInterval)
	{
		return new Core_Report_Period(
			new DateInterval($dateInterval),
			new DateTime($this->dateString)
		);
	}
	
	public function __toString()
	{
		return $this->dateString;
	}
}