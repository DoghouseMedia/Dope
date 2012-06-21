<?php

class Dope_View_Helper_FormFile extends Zend_View_Helper_FormFile
{
	public function formFile($name, $attribs = null)
	{
		$attribs = array_merge($attribs, array(
			'multiple' => 'false',
			'dojoType' => 'dope.form.Uploader',
			'id' => '',
			'type' => 'file'
		));
		
		return parent::formFile($name, $attribs);
	}
}
