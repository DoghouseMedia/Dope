<?php

class Dope_View_Helper_SenderAlias extends Zend_View_Helper_Abstract
{	
	protected $senderAlias = '';
	
	public function senderAlias($senderAlias=null)
	{
		if (!is_null($senderAlias)) {
			$this->senderAlias = $senderAlias;
		}
		
		return $this->senderAlias;
	}
}
