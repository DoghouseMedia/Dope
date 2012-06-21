<?php

class Dope_View_Helper_Dojo_Form extends Zend_Dojo_View_Helper_Form
{
	public function form($name, $attribs = null, $content = false)
	{		
		$attribs = $this->_prepareDijit($attribs, array(), 'layout');
	
		if (array_key_exists('id', $attribs)) {
			unset($attribs['id']);
		}
	
		if (array_key_exists('name', $attribs)) {
			unset($attribs['name']);
		}
				
		$attribs['data-dojo-type'] = $attribs['dojoType'];
		
		unset($attribs['dojoType']);
		unset($attribs['action']);
		unset($attribs['method']);
		unset($attribs['class']);
				
		$xhtml = '<div'
		. $this->_htmlAttribs($attribs)
		. '>';
	
		if (false !== $content) {
			$xhtml .= $content
			.  '</div>';
		}
	
		return $xhtml;
	}
}