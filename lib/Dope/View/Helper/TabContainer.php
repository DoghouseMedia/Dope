<?php

class Dope_View_Helper_TabContainer extends Zend_Dojo_View_Helper_TabContainer
{
    public function tabContainer($id = '', $content = '', array $params = array(), array $attribs = array())
    {
        if (0 === func_num_args()) {
            return $this;
        }

        return parent::tabContainer('', $content, $params, $attribs);
    }
    
	public function captureStart(array $params = array(), array $attribs = array())
    {
    	return parent::captureStart(null, $params, $attribs);
    }

    public function captureEnd()
    {
        return parent::captureEnd(null);
    }
}
