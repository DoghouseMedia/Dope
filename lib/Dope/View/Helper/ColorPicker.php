<?php

class Dope_View_Helper_ColorPicker extends Zend_Dojo_View_Helper_Dijit
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dope.form.ColorPicker';

    /**
     * HTML element type
     * @var string
     */
    protected $_elementType = 'text';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dope.form.ColorPicker';

    /**
     * dojox.widget.ColorPicker
     *
     * @param  int $id
     * @param  mixed $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function colorPicker($id, $value = null, array $params = array(), array $attribs = array())
    {
    	$attribs['id'] = '';
    	$attribs['name'] = $id;
    	
        return $this->_createFormElement('', $value, $params, $attribs);
    }
}
