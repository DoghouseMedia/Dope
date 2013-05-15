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
	
	public function getModelClassName($params=array())
	{
		/*
		 * To force use of $useOwnClassName,
		 * we set params to empty.
		 * 
		 * @todo, do this differently
		 */
		return parent::getModelClassName(array());
	}
	
	public function browseAction()
	{
		parent::browseAction();
		$this->view->modelChildrenTables = $this->getEntityRepository()->getSubClassTables();
	}
}
