<?php

class Dope_View_Helper_SenderId extends Zend_View_Helper_Abstract
{	
	protected $senderId = '';
	
	public function senderId($senderId=null)
	{
		if (!is_null($senderId)) {
			$this->senderId = $senderId;
		}
		
		return $this->senderId;
	}
}
