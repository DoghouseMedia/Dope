<?php 

namespace Dope\Controller\Action;
use \Dope\Controller\Action;

abstract class ModelChildSelector
extends Action\Model
{
	public function getModelAlias()
	{
		$classMetadata = \Dope\Doctrine::getEntityManager()->getClassMetadata(
			$this->getModelClassName()
		);

		return strtolower(str_replace(
			$classMetadata->namespace . '\\',
			'',
			$classMetadata->name
		));
	}
	
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
