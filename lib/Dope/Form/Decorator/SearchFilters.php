<?php

require_once 'Dope/Form/Decorator/Form.php';

class Dope_Form_Decorator_SearchFilters
extends Zend_Form_Decorator_HtmlTag
{
	public function render($content)
	{
		return $content . $this->getElement()->getView()->searchFilters();
	}
}