<?php

require_once 'Zend/Form/DisplayGroup.php';

class Dope_Form_DisplayGroup extends Zend_Form_DisplayGroup
{
	public function loadDefaultDecorators()
	{
		$this->setDecorators(array(
			'FormElements',
			'ContentPane'
		));
	}
	
	public function toForm($formClass=null)
	{
		$formClass = $formClass ?: get_class($this->getForm());
		 
		$form = new $formClass();
		$form->setAction($this->getForm()->getAction());
		$form->setElements($this->getElements());
		
		return $form; 
	}
	
	public function toQuickForm()
	{
		return $this->toForm('\Dope\Form\Entity\Quick')->removeLabels();
	}
	
	public function setProxy(Dope_Form_DisplayGroup $displayGroup)
	{
		$this->setDecorators(array(
			'FormElements',
			array('DisplayGroupProxy', array('foreignDisplayGroup'=>$displayGroup)),
			'ContentPane'
		));
		
		return $this;
	}
}