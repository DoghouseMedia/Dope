<?php

require_once 'Dope/View/Helper/Dojo/Form.php';

class Dope_View_Helper_QuickForm extends Dope_View_Helper_Dojo_Form
{
	/**
	 * Dijit being used
	 * @var string
	 */
	protected $_dijit  = 'dope.entity.form.Quick';
	
	/**
	 * Module being used
	 * @var string
	 */
	protected $_module = 'dope.entity.form.Quick';
	
	public function quickForm($id, $attribs = null, $content = false)
	{
		return $this->form($id, $attribs, $content);
	}
}