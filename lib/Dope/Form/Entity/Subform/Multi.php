<?php

namespace Dope\Form\Entity\Subform;
use Dope\Form\Entity\Subform,
	Dope\Form\_Base;

class Multi extends Subform
{
	protected $templateForm;
	protected $templateElementName;
	protected $relationName;
	
	public function setRelationName($relationName)
	{
		$this->relationName = $relationName;
	}
	
	public function getRelationName()
	{
		return $this->relationName;
	}
	
	public function setFormTemplate(_Base $form)
	{
		$this->templateForm = clone $form;
		$this->templateElementName = $form->getName();
	
		$form->setElementsBelongTo($this->getName());
	
		/* Remove save element */
		if ($form->hasElement('save')) {
			$form->removeElement('save');
		}
	
		$template = $form->getAsJsTemplate();
		$template->setDecorators(array('ViewHelper'));
		$template->setIgnore(true);
	
		$this->addClassName('multi-subform');
	
		return $this->addElement($template, $form->getName());
	}
	
	public function getTemplateForm()
	{
		return $this->templateForm;
	}
	
	public function getTemplateElement()
	{
		return $this->getElement($this->templateElementName);
	}
	
	public function addSubform(Subform $form, $name)
	{
		parent::addSubform($form, $name);
	
		$this->setSubFormDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			'ContentPane',
		));
	
		return $this;
	}
	
	public function populate(array $values)
	{
		parent::populate($values);
	
		foreach($values as $k => $v) {
			if (! is_array($v)) {
				continue;
			}
			
			$subForm = clone $this->getTemplateForm();
			
			if (isset($value['id']) AND !$subForm->hasElement('id')) {
				$subForm->addElement('hidden', 'id');
				$subForm->hide('id');
			}
				
			foreach ($subForm->getElements() as $element) {
				if (isset($value[$element->getName()])) {
					$element->setValue($value[$element->getName()]);
				}
			}
				
			$this->addSubForm($subForm, $key);
		}
	
		return $this;
	}
}