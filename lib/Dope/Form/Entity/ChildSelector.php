<?php

namespace Dope\Form\Entity;
use Dope\Form\Entity;

abstract class ChildSelector extends Entity
{
	public function setController(\Dope\Controller\Action $controller)
	{
		parent::setController($controller);
	
		$this->configureModelChildFields(
			$this->getController()->getEntityRepository()->getClassName()
		);
	
		return $this; // chainable
	}
	
	/* ----- Model Children ----- */
	
	public function getModelChildClass()
	{
		return $this->getValue('_type');
	}
	
	public function getModelChildControllerKey()
	{
		$className = $this->getValue('_type');
		return $this->getController()->getEntityRepository()->getModelAlias($className);
	}
	
	protected function configureModelChildFields($componentName)
	{
// 		if ($this->getController()->getEntityRepository()->hasSubClasses()) {
// 			// show only type selector
// 			$multiOptions = array();
				
// 			foreach($this->getController()->getEntityRepository()->getSubClasses() as $subModelClass) {
// 				$multiOptions[$subModelClass] = str_replace(
// 					$this->getController()->getModelClassName(),
// 					'',
// 					$subModelClass
// 				);
// 			}
				
// 			$this->addElement('FilteringSelect', '_type', array(
// 					'label' => 'Please select:',
// 					'multiOptions' => $multiOptions,
// 			));
// 		}
// 		else {
// 			// show relevant form fields
// 			$this->defineFields();
				
// 			$fieldNames = $this->getController()->getEntityRepository()->getColumnNames();
// 			\Dope\Log::console($fieldNames);
				
// 			foreach($fieldNames as $fieldName) {
// 				if ($this->hasAvailableElement($fieldName)) {
// 					$this->addElement($this->getAvailableElement($fieldName), $fieldName);
// 				}
// 			}
// 		}
	
// 		$this->addElement('submitButton', 'save', array(
// 				'required'	=> false,
// 				'ignore'	  => true,
// 				'label'	   => 'Continue',
// 				'order' => 999
// 		));
	}
	
}