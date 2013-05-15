<?php

namespace Dope\Controller\Action\Helper;

use Dope\Entity\Search,
	Dope\Doctrine,
	Dope\Config\Helper as Config;

class SavedSearch extends \Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Strategy pattern: call helper as helper broker method
	 *
	 * @param  mixed   $data
	 * @param  boolean $sendNow
	 * @param  boolean $keepLayouts
	 * @return string|void
	 */
	public function direct(Search $search)
	{
		/* Get entity class to use for Saved Search */
		$savedSearchClass = $this->getSearchClass();
		
		if (! $savedSearchClass) {
			return false;
		}
		
		/*
		 * @todo When we move to PHP 5.4 we can remove this temp var
		 * and replace the below with "function() use ($this) {}" instead
		 */
		$that = $this;
		
		/* Callback for saving when search finds IDs */
		$search->onSetIds(function($ids) use ($that) {
		    $that->save($ids);
		});
		
		/* Find Search ID (from header or request params) */
		if ($this->getRequest()->getHeader('Dope-Search-Id')) {
			$dopeSearchId = $this->getRequest()->getHeader('Dope-Search-Id');
		} elseif ($this->getRequest()->getParam('dope-search-id')) {
			$dopeSearchId = $this->getRequest()->getParam('dope-search-id');
		} else {
			$dopeSearchId = false;
		}
		
		if (! $dopeSearchId) {
			return false;
		}
		
		/* Try to fetch SavedSearch entity */
	    /** @var \Dope\Entity\SavedSearch $savedSearch */
	    $savedSearch = Doctrine::getRepository($savedSearchClass)->find($dopeSearchId);
	    
	    if (! $savedSearch instanceof $savedSearchClass) {
	    	return false;
	    }
	    
        $ids = array_slice(
            $savedSearch->results,
            $this->getRequest()->getParam('list_start'),
            $this->getRequest()->getParam('list_count')
        );
        $total = count($savedSearch->results);
        
        $search->setType(new Search\Type\ByIds($ids, $total));

        return true;
	}
	
	public function save(array $ids)
	{
		$savedSearchClass = $this->getSearchClass();
		
	    /** @var \Dope\Entity\SavedSearch $savedSearch */
	    $savedSearch = new $savedSearchClass();
	    $savedSearch->data = $this->getActionController()->getData();
	    $savedSearch->results = $ids;
	    $savedSearch->save();
	    	
	    /* Set Entity IDs Header (used by pagination) */
	    $this->getResponse()->setHeader('Dope-Search-Id',
	        $savedSearch->id
	    );
	}
	
	protected function getSearchClass()
	{
		return Config::getOption('search.saved.entityClass');
	}
}
