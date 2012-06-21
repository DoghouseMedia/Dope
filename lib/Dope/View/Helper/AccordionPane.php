<?php

class My_View_Helper_AccordionPane extends Zend_Dojo_View_Helper_AccordionPane
{
	/**
     * Create a layout container
     *
     * @param  int $id
     * @param  string $content
     * @param  array $params
     * @param  array $attribs
     * @param  string|null $dijit
     * @return string
     */
//    protected function _createLayoutContainer($id, $content, array $params, array $attribs, $dijit = null)
//    {        
//    	return parent::_createLayoutContainer($this->view->uniqueId($id), $content, $params, $attribs, $dijit);
//    }
    
	public function captureStart($id, array $params = array(), array $attribs = array())
    {
    	return parent::captureStart($this->view->uniqueId($id), $params, $attribs);
    }

    public function captureEnd($id)
    {
        return parent::captureEnd($this->view->uniqueId($id));
    }
}
