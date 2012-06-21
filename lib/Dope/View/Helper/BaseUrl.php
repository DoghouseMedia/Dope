<?php

/**
 * BaseUrl helper
 *
 * @uses helper Zend_View_Helper
 */
class My_View_Helper_BaseUrl extends Zend_View_Helper_Abstract
{
	public function baseUrl()
	{
		return Zend_Controller_Front::getInstance()->getBaseUrl();
	}
}