<?php

class Dope_View_Helper_StoreBox extends Zend_Dojo_View_Helper_FilteringSelect
{
	/**
	 * Dijit being used
	 * @var string
	 */
	protected $_dijit  = 'dope.form.StoreBox';
	
	/**
	 * Dojo module to use
	 * @var string
	 */
	protected $_module = 'dope.form.StoreBox';
	
	public function storeBox($id, $value = null, array $params = array(), array $attribs = array(), array $options = null)
    {
        return $this->filteringSelect($id, $value, $params, $attribs, $options);
    }
}
