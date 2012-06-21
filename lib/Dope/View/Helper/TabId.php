<?php

class Dope_View_Helper_TabId extends Zend_View_Helper_Abstract
{
	protected $tabId = '';
	
	public function tabId($tabId=null)
	{
		if (!is_null($tabId)) {
			$this->tabId = $tabId;
		}
		
		return $this->tabId;
	}
}
