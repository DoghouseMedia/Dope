<?php

class Dope_View_Helper_JsonRestStore extends Zend_View_Helper_Abstract
{
    /**
     * Return a Dojo QueryReadStore
     * 
     * @return mixed html | false
     */
    public function jsonRestStore($elementId=null)
    {
    	$modelAlias = $this->view->modelAlias();
    	
    	if (!$modelAlias) {
    		return false;
    	}
    	
    	/* Defaults */
    	$storeTargetParams = array(
			'controller' => $modelAlias,
			'action' => 'rest',
    		'format' => 'json'
	    );
    	
	    /* Apply params from view data */
    	if (isset($this->view->data) AND $this->view->data instanceof \Dope\Controller\Data) {
    		if ($this->view->data->sort) {
    			$storeTargetParams['sort'] = $this->view->data->sort;
    		}
    	}
    	
    	/* Apply sender info */
    	if ($this->view->senderAlias()) {
    		$storeTargetParams['sender'] = $this->view->senderAlias();
    		
    		if ($this->view->senderId()) {
    			$storeTargetParams[$this->view->senderAlias()] = $this->view->senderId();
    		}
    	}
    	
    	/* Pass the tabId, so that we can save data to the tab meta-data */
//     	if ($this->view->tabId()) {
//     		$storeTargetParams['tab'] = $this->view->tabId();
//     	}
    	
    	$storeId = $modelAlias . 'Store';
    	$storeTargetUrl = $this->view->url($storeTargetParams, null, true);
    	
    	/*
    	 * With dojo 1.5, there seems to be a really weird bug that forces us to set idAttribute to '_id' against all odds.
    	 * 
    	 * This fix was discovered by chance, and is documented nowhere (documentation will say to use 'id') 
    	 * but that does not work
    	 */
    	$html = '
			<span 
    			dojoType="dojox.data.JsonRestStore"
    			modelAlias="' . $modelAlias . '"
    			idAttribute="_id" 
    			id="' . $storeId . '"
    			jsId="' . $storeId . '"
				target="' . $storeTargetUrl . '">
    		</span>
    	';
    	
    	return $html;
    }
}
