<?php

/*
 * @todo Can this go?
 */
class Dope_Form_Decorator_ContentPane
extends Zend_Dojo_Form_Decorator_ContentPane
{	
	public function render($content)
	{
		$element = $this->getElement();
		$view    = $element->getView();
		if (null === $view) {
			return $content;
		}
	
		$dijitParams = $this->getDijitParams();
		$attribs     = array_merge($this->getAttribs(), $this->getOptions());
	
		if (array_key_exists('legend', $attribs)) {
			if (!array_key_exists('title', $dijitParams) || empty($dijitParams['title'])) {
				$dijitParams['title'] = $attribs['legend'];
			}
			unset($attribs['legend']);
		}
	
		$helper = $this->getHelper();
		return $view->$helper($content, $dijitParams, $attribs);
	}
}