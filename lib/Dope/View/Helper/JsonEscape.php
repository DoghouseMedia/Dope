<?php

class Dope_View_Helper_JsonEscape extends Zend_View_Helper_Abstract
{
	public function jsonEscape($string)
	{
		return addslashes($this->view->escape(trim($string)));
	}
}