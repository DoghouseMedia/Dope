<?php

require_once 'Dope/View/Helper/Dojo/Form.php';

class Dope_View_Helper_EntityFormContainer extends Dope_View_Helper_Dojo_Form
{
	/**
	 * Dijit being used
	 * @var string
	 */
	protected $_dijit  = 'dope.entity.FormContainer';
	
	/**
	 * Module being used
	 * @var string
	 */
	protected $_module = 'dope.entity.FormContainer';
	
	public function entityFormContainer($id, $attribs = null, $content = false)
	{
		return $this->form($id, $attribs, $content);
	}
}