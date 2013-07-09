<?php

namespace Dope\Form;

use Dope\Config\Helper as Config;

require_once 'Dope/Form/DisplayGroup.php';

class _Base extends \Zend_Dojo_Form
{	
	/**
	 * @var \Dope\Controller\Action
	 */
	protected $controller;
	
	/**
	 * @var array
	 */
	protected $params = array();
	
	/**
	 * @var array
	 */
	protected $dojoProps = array();
	
	/**
	 * Constructor
	 *
	 * @param  array|Zend_Config|null $options
	 * @return void
	 */
	public function __construct($options = null)
	{
		// 		if (!$this instanceof Core_Form_Subform) {
		// 			$this->addElement('hidden', 'deleted');
		// 			$this->deleted->setRequired(false);
		// 			$this->deleted->setAttrib('class', 'deleted');
		// 			$this->hide('deleted', '0');
		// 		}
		
		/* We need to include Dojo mods before ours */
		$this->addPrefixPath('Zend_Dojo_Form_Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
			->addPrefixPath('Zend_Dojo_Form_Element', 'Zend/Dojo/Form/Element', 'element')
			->addElementPrefixPath('Zend_Dojo_Form_Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
			->addDisplayGroupPrefixPath('Zend_Dojo_Form_Decorator', 'Zend/Dojo/Form/Decorator');
		
		/* Add Dope */
		$this->addPrefixPath('Dope_Form', 'Dope/Form/')
			->addElementPrefixPath('Dope_Form_Decorator', 'Dope/Form/Decorator', 'decorator');;
		
		/* Add application form prefixes */	
		$this->addPrefixPath(
			Config::getOption('appnamespace') . '_Form', 
			Config::getOption('appnamespace') . '/Form/'
		);
		
		parent::__construct($options);
		
		$this->setDefaultDisplayGroupClass('Dope_Form_DisplayGroup');
		
		$this->setElementDecorators(array(
			'DijitElement',
			'Label',
			'Errors',
			array('HtmlTag', array('class' => 'field'))
		));
	}
	
	/* ----- CONTROLLER ----- */
	
	public function hasController()
	{
		return ($this->controller instanceof \Dope\Controller\Action);
	}
	
	/**
	 * @return \Dope\Controller\Action
	 */
	public function getController()
	{
		return $this->controller;
	}
	
	public function setController(\Dope\Controller\Action $controller)
	{
		$this->controller = $controller;
		
		if ($this->getController()->view) {
			$this->setView($this->getController()->view);
		}
		
		$params = $this->getController()->getRequest()->getParams();
		$isPost = $this->getController()->getRequest()->isPost();
		$isPut = $this->getController()->getRequest()->isPut();
		$hasDataInFormParam = isset($params[$this->getName()]);
		if (($isPost OR $isPut) AND $hasDataInFormParam) {
			$params = $params[$this->getName()];
		}
		$this->setParams($params);
		
		return $this;
	}
	
	/**
	 * @return \Dope\Doctrine\ORM\EntityRepository
	 */
	public function getEntityRepository()
	{
		if (! $this->hasController()) {
			return false;
		}
	
		return $this->getController()->getEntityRepository();
	}
	
	public function preConfigure()
	{
		/* Add Sender field */
		if (! $this->hasElement('sender')) {
			$this->addElement('hidden', 'sender', array(
				'decorators' => array('DijitElement')
			));
		}
		
		return $this;
	}
	
	public function addSubmitButton()
	{
		/* Add submit button */
		if (! $this->hasElement('submit')) {
			$this->addElement('submitButton', 'submit', array(
				'required' => false,
				'ignore' => true,
				'label' => "Submit",
				'class' => "fat",
				'value' => 'submit',
				'decorators' => array('DijitElement')
			));
		}
		
		return $this->getElement('submit');
	}
	
	public function configure()
	{
		$this->preConfigure();
		$this->populate();
		$this->postConfigure();
		return $this;
	}
	
	public function postConfigure()
	{
		$this->addSubmitButton();
		
		/*
		 * This is a ~kind of~ dirty hack to force forms that do not follow
		 * our models (eg. the mailer) to include the senderId in the form if the sender param is present
		 * @todo 2013-07-09 This might not be needed any more?
		 */
		if ($this->hasElement('sender') 										// form has sender
			AND $this->getElement('sender')->getValue()							// sender has value (eg. candidate)
			AND !$this->hasElement($this->getElement('sender')->getValue())		// form does NOT have senderId (eg: candidate => ?)
			AND $this->hasParam($this->getElement('sender')->getValue())		// params has senderId (eg. candidate)
			AND is_string($this->getParam($this->getElement('sender')->getValue()))		// sender IS string
		){
			$this->addElement('hidden', $this->getElement('sender')->getValue(), array(
				'value' => $this->getParam($this->getElement('sender')->getValue()),
				'decorators' => array('DijitElement')
			));
		}
		
		/* Enctype */
		foreach($this->getElements() as $element) {
			if ($element instanceof \Zend_Form_Element_File) {
				$this->setAttrib('enctype', 'multipart/form-data');
			}
		}
	
		return $this; // chainable
	}

	/* -----| Subforms |----- */
	
	public function addSubForm(_Base $form, $name, $order=null)
	{
		parent::addSubForm($form, $name, $order);
	
		if ($form instanceof Subform) {
			$form->setParentForm($this);
		}
	
		/* Set IsArray */
		$this->getSubForm($name)->setIsArray(true);
	
		/* Remove save element */
		if ($form->hasElement('save')) {
			$form->removeElement('save');
		}
	
		$this->setSubFormDecorators(array(
			'FormElements',
			array('HtmlTag', array(
				'tag' => 'div',
				'class' => 'subform-container'
			)),
			'ContentPane',
		));
	
		return $this;
	}
	
	/* -----| Html Classes |----- */
	
	public function getClassNames()
	{
		return explode(' ', $this->getAttrib('class'));
	}
	
	public function setClassNames(array $classNames)
	{
		$this->setAttrib('class',
			join(' ', array_unique($classNames))
		);
		return $this; // chainable
	}
	
	public function hasClassName($className)
	{
		return in_array($className, $this->getClassNames());
	}
	
	public function addClassName($className)
	{
		$classNames = $this->getClassNames();
		$classNames[] = $className;
		return $this->setClassNames($classNames);
	}
	
	public function removeClassName($className)
	{
		$classNames = $this->getClassNames();
		$classIndex = array_search($className, $classNames);
		unset($classNames[$classIndex]);
		return $this->setClassNames($classNames);
	}
	
	/* -----| JS methods |----- */
	
	/**
	 * Get as JS template
	 *
	 * We use this when we need a subform as a JS template
	 * to add more dynamically using JS.
	 *
	 * @return \Dope\Form\Element\Js\Template
	 */
	public function getAsJsTemplate()
	{
		$templateElement = new Element\Js\Template($this->getAttrib('name'));
		$templateElement->setForm($this);
	
		return $templateElement;
	}
	
	/* ----- Data ----- */
	
	/**
	 * Overriding the populate method because there's a number of times where
	 * we need to change data sources for dropdowns after we've populated the form.
	 *
	 * @param array $values
	 */
	public function populate(array $values=null)
	{
		if (! is_array($values)) {
			$values = $this->getParams();
		}
		
		$values = $this->filterDatesToISO8601($values);
	
		parent::populate($values);
	
		\Dope\Log::console("Populate form " . get_class($this));
		\Dope\Log::console(array_keys((array) $values));
	
		foreach($values as $k => $v) {
			if (
				($this->getElement($k) instanceof \Zend_Dojo_Form_Element_ComboBox) &&
				(!$this->getElement($k)->getStoreInfo() && !$this->getElement($k)->registerInArrayValidator()) &&
				(count($this->getElement($k)->getMultiOptions()) == 0)
			){
				$this->getElement($k)->addMultiOption($v)->setValue($v);
			}
		}
	
		return $this;
	}
	
	public function addElement($element, $name = null, array $options = array())
	{
		$options['id'] = '';
		
		if (!isset($options['placeholder']) AND isset($options['label'])) {
			$options['placeholder'] = $options['label'];
		}
		
		return parent::addElement($element, $name, $options);
	}
	
	/* ----- Helpers ----- */
	
	public function hasElement($name)
	{
		return (bool) $this->getElement($name);
	}
	
	public function hide($name, $value=null)
	{
		$value = $value ?: $this->getElement($name)->getValue();
	
		$decorators = array('ViewHelper');
	
		if ($this->getElement($name)->getType() == 'Zend_Form_Element_Hidden') {
			$this->getElement($name)
				->setDecorators($decorators)
				->setValue($value);
		}
		else {
			$validators = $this->getElement($name)->getValidators();
	
			$this->removeElement($name);
	
			$this->addElement('hidden', $name, array(
				'value' => $value,
				'decorators' => $decorators,
				'validators' => $validators
			));
		}
	
		return $this; // allow chaining
	}
	
	public function isHidden($name) {
		return ($this->getElement($name)->getType() == 'Zend_Form_Element_Hidden');
	}
	
	public function getValues($suppressArrayNotation=false)
	{
		$values = parent::getValues($suppressArrayNotation);
	
		\Dope\Log::console($values);
	
		foreach($values as $key => $val) {
			$isSubform = (bool) $this->getSubForm($key);
			$isArrayValues = is_array($val);
			$isNumericKeys = ($isArrayValues AND (is_int($key) OR is_int(current(array_keys($val)))));
				
			if ($isSubform AND $isArrayValues AND !$isNumericKeys) {
				foreach($val as $k => $v) {
					$values[$k] = $v;
				}
	
				unset($values[$key]);
			}
		}
	
		return $values;
	}
	
	public function removeLabels()
	{
		foreach ($this->getElements() as $element) {
			$element->removeDecorator('Label');
		}
		return $this;
	}
	
	public function getErrors()
	{
		$formErrors = parent::getErrors();
	
		if ($this->getName()) {
			$formErrors = $formErrors[$this->getName()];
		}
	
		$errors = array();
	
		foreach($formErrors as $fieldName => $fieldErrors) {
			foreach($fieldErrors as $fieldError) {
				$errors[] = $fieldName . ': ' . $fieldError;
			}
		}
	
		return $errors;
	}
	
	/* ----- Dates ----- */
	
	public function filterDatesToISO8601(array $values)
	{
		$date = new \Zend_Date();
	
		foreach ($this->getElements() as $field) {
			if ($field->helper != 'DateTextBox') {
				continue;
			}
			
			$fieldName = $field->getName();
	
			if (! isset($values[$fieldName])) {
				continue;
			}
			
			if (empty($values[$fieldName]) AND !$field->required) {
				continue;
			}
			
			if ($values[$fieldName] instanceof \DateTime) {
				$values[$fieldName] = $values[$fieldName]->format('Y-m-d H:i:s');
			}
			elseif ($date->set($values[$fieldName], \Zend_Date::ISO_8601)) {
				$values[$fieldName] = $date->getIso();
			}
		}
	
		foreach($this->getSubForms() as $form) {
			$values = $form->filterDatesToISO8601($values);
		}
	
		return $values;
	}
	
	
	public function setParams(array $params)
	{
		$this->params = $params;
		return $this;
	}
	
	public function getParams()
	{
		return $this->params;
	}
	
	public function getParam($key)
	{
		return $this->hasParam($key) ? $this->params[$key] : false;
	}
	
	public function hasParam($key)
	{
		return isset($this->params[$key]);
	}
	
	public function setDojoRegion($region)
	{
		return $this->setDojoProp('region', 'top');
	}
	
	public function setDojoProp($key, $val)
	{
		$this->dojoProps[$key] = $val;
		return $this;
	}
	
	public function setDojoProps(array $props=array())
	{
		$this->dojoProps = $props;
		return $this;
	}
	
	public function getDojoProps()
	{
		return $this->dojoProps;
	}
}
