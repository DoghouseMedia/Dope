<?php

namespace Dope\Form\Entity;
use \Dope\Form\_Base;

class Subform extends \Dope\Form\Entity
{
	/**
	 * Parent Form
	 * @var \Dope\Form\_Base
	 */
	protected $parentForm;
	
	public function setParentForm(_Base $form)
	{
		$this->parentForm = $form;
		return $this;
	}
	
	public function hasParentForm()
	{
		return (bool) $this->getParentForm();
	}
	
	public function getParentForm()
	{
		return $this->parentForm;
	}
	
	public function toForm()
	{
		$form = clone $this;
		$parentForm = $form->getParentForm();
		
		$form->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			'Form'
		));
		
		if ($parentForm->hasController()) {
			$form->setAction($parentForm->getAction() . '/subformname/' . $form->getName());
			$form->setController($parentForm->getController());
		}
		
		$form->setView($parentForm->getView());
		
		return $form;
	}
}