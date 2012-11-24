<?php

class Dope_View_Helper_Url extends Zend_View_Helper_Url
{
    /**
     * Generates an url given the name of a route.
     *
     * @access public
     *
     * @param  array $urlOptions Options passed to the assemble method of the Route object.
     * @param  mixed $name The name of a Route to use. If null it will use the current Route
     * @param  bool $reset Whether or not to reset the route defaults with those provided
     * @return string Url for the link href attribute.
     */
    public function url(array $urlOptions = array(), $name = null, $reset = true, $encode = false, $propagateParentTabId = false)
    {
    	if (count($urlOptions)) {
	    	/* Remove 0-length (empty) entries */
	        $urlOptions = array_filter($urlOptions, 'strlen');
	        
	        /* Urlencode values */
	        $urlOptions = array_combine(
	        	array_keys($urlOptions),
	        	array_map('rawurlencode', array_values($urlOptions))
	        );
    	}
        
        return parent::url($urlOptions, $name, $reset, $encode);
    }
}
