<?php

namespace Dope\Report;

class _Base
{
    const FORM_CLASS = 'Dope\Report\Form\_Base';
    
    protected $timeRun;
    protected $timeStart;
    protected $timeEnd;
    
    protected $results;
    
    /**
     * Form
     *
     * @var Core_Form
     */
    protected $form;
    
    public function __construct(\Dope\Controller\Action $controller)
    {
        $this->getForm()->setController($controller);
        $this->timeStart();    
    }
    
    public function getTitle()
    {
        return static::TITLE;
    }
    
    public function timeStart()
    {
        $this->timeStart = microtime(true);
    }
    
    public function timeEnd()
    {
        $this->timeEnd = microtime(true);
    }
    
    public function getTimeRun()
    {
        if (! $this->timeEnd) {
            $this->timeEnd();
        }
        
        return $this->timeEnd - $this->timeStart;
    }
    
    public function getForm()
    {
        if (! $this->form instanceof \Dope\Report\Form\_Base) {
            $formClass = static::FORM_CLASS;
            $this->form = new $formClass;
        }
    
        return $this->form;
    }
    
    public function getResults()
    {
        return $this->results;
    }
}