<?php

namespace Dope\Report;

abstract class Filter
{
    private $alias = '';
    private $isRequired = false;
    
    /**
     * @var \Dope\Report\_Base
     */
    protected $report;
    
    public function __construct($alias = '')
    {
        $this->setAlias($alias);
    }
    
    public function getAlias()
    {
        return $this->alias;
    }
    
    public function setAlias($alias = '')
    {
        $this->alias = $alias;
        return $this;
    }

    public function isRequired($isRequired=null)
    {
        if (is_bool($isRequired)) {
            $this->isRequired = $isRequired;
        }

        return (bool) $this->isRequired;
    }

    /**
     * @return \Dope\Report\_Base
     */
    public function getReport()
    {
        return $this->report;
    }
    
    public function setReport(_Base $report)
    {
        $this->report = $report;
        
        $this->init();
        
        return $this;
    }
    
    abstract public function init();
}