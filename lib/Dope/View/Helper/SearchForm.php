<?php

require_once 'Dope/View/Helper/Dojo/Form.php';

class Dope_View_Helper_SearchForm extends Dope_View_Helper_Dojo_Form
{
	/**
	 * Dijit being used
	 * @var string
	 */
	protected $_dijit  = 'dope.search.Form';
	
	/**
	 * Module being used
	 * @var string
	 */
	protected $_module = 'dope.search.Form';
	
	public function searchForm($id, $attribs = null, $content = false)
	{
		return $this->form($id, $attribs, $content);
	}
}