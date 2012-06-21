<?php

class Dope_View_Helper_FormHidden extends Zend_View_Helper_FormHidden
{
	public function formHidden($name, $value = null, array $attribs = null)
	{
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable
		return $this->_hidden($name, $value, $attribs);
	}
}
