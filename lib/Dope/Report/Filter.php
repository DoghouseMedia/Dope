<?php

namespace Dope\Report;

abstract class Filter
{
    protected $alias = '';
    
    /**
     * @var \Dope\Report\_Base
     */
    protected $report;
    
    public function __construct($alias = '', _Base $report = null)
    {
        $this->setAlias($alias);
        
        if ($report) {
            $this->setReport($report);
        }
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