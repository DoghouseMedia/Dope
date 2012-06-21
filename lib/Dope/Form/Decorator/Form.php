<?php

class Dope_Form_Decorator_Form extends Zend_Dojo_Form_Decorator_DijitForm
{
	/**
	 * Default view helper
	 * @var string
	 */
	protected $_helper = 'form';
	
	/**
	 * Set current form element
	 *
	 * @param  Zend_Form_Element|Zend_Form $element
	 * @return Zend_Form_Decorator_Abstract
	 * @throws Zend_Form_Decorator_Exception on invalid element type
	 */
	public function setElement($element)
	{
		$props = array(
			"encType:'" . $element->getAttrib('enctype') ."'",
			"formName:'" . $element->getName() . "'",
			"action:'" . $element->getAction() . "'",
			"method:'" . $element->getMethod() . "'"
		);
		foreach ($element->getDojoProps() as $key => $val) {
			$props[] = "$key: '$val'";
		}
		$element->setAttrib('data-dojo-props', join(',', $props));
		$element->setAttrib('id', '');
		
		return parent::setElement($element);
	}
	
	/**
	 * Render a form
	 *
	 * Replaces $content entirely from currently set element.
	 *
	 * @param  string $content
	 * @return string
	 */
	public function render($content)
	{
		$element = $this->getElement();
		$view    = $element->getView();
		if (null === $view) {
			return $content;
		}
	
		$dijitParams = $this->getDijitParams();
		$attribs     = array_merge($this->getAttribs(), $this->getOptions());
	
		return $view->{$this->_helper}($element->getName(), $attribs, $content);
	}
}