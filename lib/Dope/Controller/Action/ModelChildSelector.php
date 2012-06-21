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
	
	public function newAction()
	{
		//throw new \Exception("You can't create a new master record.");
// 		if ($this->getRequest()->isPost()) {
// 			if ($this->getEntityForm()->getModelChildClass()) {
// 				$controllerKey = $this->getEntityForm()->getModelChildControllerKey();
				
// 				$data = $this->getData($this->getEntityForm()->getValues(true));
// 				$data->removeParam('_type');
				
// 				$this->view->tabId(false); // stop tab from propagating
// 				$this->view->open = $this->view->url(array_merge((array) $data->getParams(), array(
// 					'controller' => $controllerKey,
// 					'action' => 'new',
// 					'format' => 'html'
// 				)));
// 				$this->view->title = (string) 'New ' . $controllerKey;
// 				$this->view->status = true;

// 		    	return $this->respondOk();
// 			}
// 		}

		parent::newAction();
	}
	
	public function browseAction($returnCollection=false)
	{
		parent::browseAction($returnCollection);
		$this->view->modelChildrenTables = $this->getEntityRepository()->getSubClassTables();
	}
}
