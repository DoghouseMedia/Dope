<?php

class Dope_View_Helper_BorderContainer extends Zend_Dojo_View_Helper_BorderContainer
{
	public function borderContainer($id = null, $content = '', array $params = array(), array $attribs = array())
	{
		if (0 === func_num_args()) {
            return $this;
        }
        
		return parent::borderContainer(null, $content, $params, $attribs);
	}
	
	public function captureStart($id='', array $params = array(), array $attribs = array())
    {
    	/* Add some classes to borderContainer if record */
    	if ($this->view->record) {
    		if (! isset($params['class'])) {
    			$params['class'] = '';
    		}
    		
    		$params['class'] .= $this->view->generateRecordClasses($this->view->record);
    	}

    	return parent::captureStart('', $params, $attribs);
    }

    public function captureEnd($id='')
    {
        return parent::captureEnd('');
    }
}
