<?php

class Dope_View_Helper_Dojo 
extends Zend_Dojo_View_Helper_Dojo
{
	/**
     * Initialize helper
     *
     * Retrieve container from registry or create new container and store in
     * registry.
     *
     * @return void
     */
    public function __construct()
    {
        $registry = Zend_Registry::getInstance();
        if (!isset($registry[__CLASS__])) {
            require_once 'Dope/View/Helper/Dojo/Container.php';
            $container = new Dope_View_Helper_Dojo_Container();
            $registry[__CLASS__] = $container;
        }
        $this->_container = $registry[__CLASS__];
    }
}