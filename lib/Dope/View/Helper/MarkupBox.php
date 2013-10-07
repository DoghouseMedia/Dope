<?php

class Dope_View_Helper_MarkupBox
extends \Zend_Dojo_View_Helper_NumberTextBox
{
	/**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dope.form.MarkupBox';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dope.form.MarkupBox';
    
    public function markupBox($id='', $value = null, array $params = array(), array $attribs = array())
    {
    	return $this->numberTextBox($id, $value, $params, $attribs);
    }
}