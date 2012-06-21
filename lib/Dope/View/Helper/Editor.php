<?php

class Dope_View_Helper_Editor extends Zend_Dojo_View_Helper_Editor
{
	public function editor($id, $value = null, $params = array(), $attribs = array())
	{
		$attribs['id'] = uniqid('dijit_editor_');
		$attribs['data-dojo-type'] = 'dope.form.Editor';
		$attribs['data-dojo-props'] = "name: '$id'";
		
		return '<div' . $this->_htmlAttribs($attribs) . '>'
			. $value
			. '</div>';
	}
}