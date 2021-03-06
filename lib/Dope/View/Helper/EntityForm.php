<?php

require_once 'Dope/View/Helper/Dojo/Form.php';

class Dope_View_Helper_EntityForm extends Dope_View_Helper_Dojo_Form
{
	/**
	 * Dijit being used
	 * @var string
	 */
	protected $_dijit  = 'dope.entity.Form';
	
	/**
	 * Module being used
	 * @var string
	 */
	protected $_module = 'dope.entity.Form';
	
	public function entityForm($id, $attribs = null, $content = false)
	{
		return $this->form($id, $attribs, $content);
	}
}