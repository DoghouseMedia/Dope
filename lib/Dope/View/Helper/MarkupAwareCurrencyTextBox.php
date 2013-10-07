<?php

class Dope_View_Helper_MarkupAwareCurrencyTextBox
extends \Zend_Dojo_View_Helper_CurrencyTextBox
{
	/**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dope.form.MarkupAwareCurrencyTextBox';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dope.form.MarkupAwareCurrencyTextBox';
    
    public function markupAwareCurrencyTextBox($id='', $value = null, array $params = array(), array $attribs = array())
    {
    	return $this->currencyTextBox($id, $value, $params, $attribs);
    }
}