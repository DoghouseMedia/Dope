<?php

class Dope_View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
	/**
	 * Core_Acl
	 * @var Core_Acl $acl
	 */
	protected $acl;
	
	/**
	 * Set ACL
	 * @param Core_Acl $acl
	 */
	public function setAcl(\Dope\Auth\Acl $acl)
    {
    	$this->acl = $acl;
    }
    
    public function isAllowed($resource=null, $privilege=null)
    { 
    	if (is_null($resource) && is_null($privilege)) {
    		return $this;
    	}
    	
    	if (! $this->acl instanceof \Dope\Auth\Acl) {
    		return false;
    	}
    	
        return $this->acl->isAllowed(
        	\Dope\Auth\Service::getUser()->role,
        	$resource,
        	$privilege
        );
    }
}
