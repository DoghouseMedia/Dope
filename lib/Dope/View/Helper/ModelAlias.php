<?php

class Dope_View_Helper_ModelAlias extends Zend_View_Helper_Abstract
{	
	protected $modelAlias = '';
	
	public function modelAlias($modelAlias=null)
	{
		if (!is_null($modelAlias)) {
			$this->modelAlias = $modelAlias;
		}
		
		return $this->modelAlias;
	}
}
