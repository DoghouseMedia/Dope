<?php 

namespace Dope\Controller\Action;
use \Dope\Controller\Action;

abstract class ModelChildSelector
extends Action\Model
{
	public function getModelClassName($params=array(), $forceUseOwnClassName=true)
	{
		return parent::getModelClassName($params, $forceUseOwnClassName);
	}
	
	public function browseAction()
	{
		parent::browseAction();
		$this->view->modelChildrenTables = $this->getEntityRepository()->getSubClassTables();
	}
}
