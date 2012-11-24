<?php

namespace Dope\Report;

class _Base
{
    const FORM_CLASS = 'Dope\Report\Form\_Base';
    
    protected $timeRun;
    protected $timeStart;
    protected $timeEnd;
    
    protected $results;
    
    protected $queryBuilder;
    
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
        if (! $this->results) {
            $query = $this->getQueryBuilder()->getQuery();
            
            $query->useResultCache(base64_encode($query->getSQL()));
            $query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
            
            $this->results = $query->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
        }
        
        return $this->results;
    }
    
    public function getQueryBuilder()
    {
        if (! $this->queryBuilder) {
            $this->queryBuilder = \Dope\Doctrine::getEntityManager()->createQueryBuilder();
        }
        
        return $this->queryBuilder;
    }
    
    
    public function render()
    {
        $view = $this->getForm()->getController()->view;
        
        /*
         * We'll use an array so we can use join at the end
         * to insert line breaks, instead of having to concat
         * every single line with a line break. Probably faster,
         * and definitely more readable.
         */
        $html = array();
        
        $html[] = '<table>';
        $html[] = '<thead>';
        $html[] = '<tr>';
        foreach (array_keys($this->columns) as $label) {
            $html[] = '<th>';
            $html[] = $label;
            $html[] = '</th>';
        }
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach($this->getResults() as $entity) {
            $html[] = '<tr>';
            foreach (array_values($this->columns) as $valueRenderer) {
                $html[] = '<td>';
                $html[] = $valueRenderer($entity, $view);
                $html[] = '</td>';
            }
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return join(PHP_EOL, $html);
    }
}