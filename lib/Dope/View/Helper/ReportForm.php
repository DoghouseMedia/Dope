<?php

require_once 'Dope/View/Helper/Dojo/Form.php';

class Dope_View_Helper_ReportForm extends Dope_View_Helper_Dojo_Form
{
	/**
	 * Dijit being used
	 * @var string
	 */
	protected $_dijit  = 'dope.report.Form';
	
	/**
	 * Module being used
	 * @var string
	 */
	protected $_module = 'dope.report.Form';
	
	public function reportForm($id, $attribs = null, $content = false)
	{
		return $this->form($id, $attribs, $content);
	}
}