<?php
/**
 * QueryReadStore view helper - all this really does is returns the HTML to insert a QueryReadStore, it's just to clean up the views / avoid repeating code.
 * 
 * @uses      Zend_View_Helper_Abstract
 * @package   GAWDAMN
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 * @version   $Id: $
 */
class Dope_View_Helper_GenerateQueryString extends Zend_View_Helper_Abstract
{
	/**
     * Convert the query values into a string and return it. 
     * 
     * @return string
     */
    public function generateQueryString($values = array(), Core_Form $form=null, $stopOnEmptyValues=true)
    {
    	if (is_null($form))
    	{
    		$stopOnEmptyValues = false;
    	}
    	
    	/*
    	 * If the $stopOnEmptyValues flag is true, we want to make sure all values are empty, and then return.
    	 * 
    	 * If we find a non-empty value, we reset $stopOnEmptyValues to false
    	 */
    	if ($stopOnEmptyValues)
    	{
    		foreach($values as $k => $v)
    		{
    			if ($v != '')
    			{
    				$stopOnEmptyValues = false;
    				break;
    			}
    		}
    		
    		if ($stopOnEmptyValues)
    		{
    			return false;
    		}
    	}
    	
    	/*
    	 * If the form has a select field, add it to the values for building the ajax query
    	 */
    	if ($form instanceof Core_Form AND $form->hasElement('select') AND !isset($values['select']))
    	{
    		$values['select'] = $form->select->getValue();
    	}
    	
    	/*
    	 * Process values
    	 */
    	$queryStrings = $this->processValues((array) $values, array());

    	return "{ " . implode(", ", $queryStrings) . " }";
    }
    
    protected function processValues(array $values, array $queryStrings)
    {
    	foreach($values as $key => $value)
    	{
	    	if (is_array($value))
	    	{
				$queryStrings = $this->processValues($value, $queryStrings);
	    	}
	    	elseif (is_string($value) OR is_numeric($value))
	    	{
	    		//$queryStrings[] = $key . ": '" . addslashes(htmlentities($value)) . "' || '*'";
	    		$queryStrings[] = $key . ": '" . addslashes(htmlentities($value)) . "'";
	    	}
    	}
    	
    	return $queryStrings;
    }
}
