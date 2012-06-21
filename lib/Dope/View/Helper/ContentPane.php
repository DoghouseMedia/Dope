<?php

class Dope_View_Helper_ContentPane extends Zend_Dojo_View_Helper_ContentPane
{
	/**
	 * Dijit being used
	 * @var string
	 */
	protected $_dijit  = 'dope.layout.ContentPane';
	
	/**
	 * Module being used
	 * @var string
	 */
	protected $_module = 'dope.layout.ContentPane';
    
	public function contentPane($content = '', $params = array(), array $attribs = array())
	{
		if (0 === func_num_args()) {
			return $this;
		}
	
		return $this->_createLayoutContainer('', $content, $params, $attribs);
	}
	
	public function captureStart(array $params = array(), array $attribs = array())
    {
    	return parent::captureStart('', $params, $attribs);
    }

    public function captureEnd()
    {
    	return parent::captureEnd('');
    }
}
