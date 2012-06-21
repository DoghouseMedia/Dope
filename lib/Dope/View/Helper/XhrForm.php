<?php

require_once 'Dope/View/Helper/Dojo/Form.php';

class Dope_View_Helper_XhrForm extends Dope_View_Helper_Dojo_Form
{
	/**
	 * Dijit being used
	 * @var string
	 */
	protected $_dijit  = 'dope.xhr.Form';
	
	/**
	 * Module being used
	 * @var string
	 */
	protected $_module = 'dope.xhr.Form';
	
	public function xhrForm($id, $attribs = null, $content = false)
	{
		return $this->form($id, $attribs, $content);
	}
}