<?php

namespace Dope\Entity\Search\Table;

class Aliases extends \ArrayObject
{
    public function getNewAlias($isUsed = null)
    {
        $alias = new Alias($isUsed);
        $this->append($alias);
        return $alias;
    }
    
    public function getUsed()
    {
    	$used = array();
    	
    	foreach($this as $alias) {
    		if ($alias->isUsed()) {
    			$used[] = $alias;
    		} 
    	}
    	
    	return $used;
    }
}