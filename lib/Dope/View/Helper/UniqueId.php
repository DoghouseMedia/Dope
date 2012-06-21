<?php

class Dope_View_Helper_UniqueId extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 */
	public function uniqueId($name)
	{
		$uniqueIdParts = array();
		
// 		if ($this->view->tabId()) {
// 			$uniqueIdParts[] = $this->view->tabId();
			
// 			if ($this->view->modelAlias()) {
// 				$uniqueIdParts[] = $this->view->modelAlias();
// 			}
// 		}
		
		$uniqueIdParts[] = $name;
		
		$uniqueIdParts = array_filter($uniqueIdParts, 'strlen');
		
		return join('--', $uniqueIdParts);
	}
}
