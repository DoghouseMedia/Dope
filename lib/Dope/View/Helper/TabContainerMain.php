<?php

require_once 'Dope/View/Helper/TabContainer.php';

class Dope_View_Helper_TabContainerMain extends Dope_View_Helper_TabContainer
{
	/**
	 * Dijit being used
	 * @var string
	 */
	protected $_dijit  = 'dope.layout.TabContainerMain';
	
	/**
	 * Dojo module to use
	 * @var string
	 */
	protected $_module = 'dope.layout.TabContainerMain';
	
	public function tabContainerMain()
	{
		return $this;
	}
	
	public function captureStart(array $params = array(), array $attribs = array())
	{
		return parent::captureStart($params, $attribs);
	}
	
	public function captureEnd()
	{
		return parent::captureEnd();
	}
}
