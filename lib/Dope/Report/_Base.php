<?php

namespace Dope\Report;

abstract class _Base
{
    const FORM_CLASS = 'Dope\Report\Form';
    const DEFAULT_SORT_COLUMN = true;
    
    protected $timeRun;
    protected $timeStart;
    protected $timeEnd;
    
    protected $defaultSortColumn;
    protected $columns = array();
    protected $results;
    protected $queryBuilder;
    
    /**
     * @var \Dope\Controller\Action
     */
    protected $controller;
    
    /**
     * Form
     *
     * @var \Dope\Report\Form
     */
    protected $form;
    
    public function __construct(\Dope\Controller\Action $controller)
    {
        $this->setController($controller);
        $this->timeStart();    
    }
    
    public function addColumn(Column $column, $isDefaultSortColumn=false)
    {
        $this->columns[] = $column;
        
        if ($isDefaultSortColumn) {
            $this->setDefaultSortColumn($column);
        }
        
        return $this;
    }
    
    public function setDefaultSortColumn(Column $column)
    {
        $this->defaultSortColumn = $column;
        return $this;
    }
    
    public function addFilter(Filter $filter)
    {
        $filter->setReport($this);
        $this->filters[] = $filter;
        return $this;
    }
    
    public function getUrlKey()
    {
        return static::URL_KEY;
    }
    
    /**
     * @return \Dope\Controller\Action
     */
    public function getController()
    {
        return $this->controller;
    }
    
    public function setController(\Dope\Controller\Action $controller)
    {
        $this->controller = $controller;
        $this->getForm()->setController($controller);
        return $this;
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
    
    /**
     * 
     * @return \Dope\Report\Form\_Base
     */
    public function getForm()
    {
        if (! $this->form instanceof \Dope\Report\Form) {
            $formClass = static::FORM_CLASS;
            $this->form = new $formClass;
        }
    
        return $this->form;
    }
    
    public function getResults()
    {
        if (! $this->results) {
            $sorts = $this->getController()->getData()->sort ?: $this->defaultSortColumn->getSort();
            $sortOrder = $this->getController()->getData()->sort_order ?: 'ASC';
            
            foreach (explode(',', $sorts) as $sort) {
                $this->getQueryBuilder()->addOrderBy($sort, $sortOrder);
            }
            
            $query = $this->getQueryBuilder()->getQuery();
            
            $query->useResultCache(base64_encode($query->getSQL()));
            $query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
            
            $this->results = $query->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
        }
        
        return $this->results;
    }
    
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        if (! $this->queryBuilder) {
            $this->queryBuilder = \Dope\Doctrine::getEntityManager()->createQueryBuilder();
        }
        
        return $this->queryBuilder;
    }
    
    public function toArray()
    {
        $array = array();
        $view = $this->getForm()->getController()->view;
        
        foreach($this->getResults() as $entity) {
            $row = array();
            foreach ($this->columns as $column) {
                $row[$column::LABEL] = $column->renderPlain(
                    $column->getEntityByAccessor($entity),
                    $view
                );
            }
            $array[] = $row;
        }
        
        return $array;
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
        foreach ($this->columns as $column) {
            $html[] = '<th>';
            $html[] = $this->renderLabel($column);
            $html[] = '</th>';
        }
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach($this->getResults() as $entity) {
            $html[] = '<tr>';
            foreach ($this->columns as $column) {
                $html[] = '<td>';
                $html[] = $column->render(
                    $column->getEntityByAccessor($entity),
                    $view
                );
                $html[] = '</td>';
            }
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return join(PHP_EOL, $html);
    }
    
    public function renderLabel(Column $column)
    {
        $classes = array('sort');
        $data = clone $this->getController()->getData();
        
        $isCurrent = (
            ($data->sort == $column->getSort()) OR 
            (!$data->sort AND $this->defaultSortColumn == $column)
        );
        
        if ($isCurrent) {
            $classes[] = 'sort-current';
        } else {
            $data->setParam('sort', $column->getSort());
        }
        
        if ($data->sort_order == 'ASC') {
            $data->setParam('sort_order', 'DESC');
            $classes[] = 'sort-desc';
        } else {
            $data->setParam('sort_order', 'ASC');
            $classes[] = 'sort-asc';
        }
    
        $urlParams = (array) $data->getParams(true);
        $urlParams['sort'] = $data->sort;
        $urlParams['sort_order'] = $data->sort_order;
        
        $url = $this->getForm()->getView()->url(array(), null, false) . '?' . http_build_query($urlParams);
    
        //foreach ($urlParams as $k => $v) {
            //$url .= $this->getForm()->getElementsBelongTo() . '[' . $k . ']=' . urlencode($v) . '&';
            //$url .= $k . '=' . urlencode($v) . '&';
        //}
    
        return '<a class="' . join(' ', $classes) . '" href="' . $url . '">' . $column::LABEL . '</a>';
    }
    
    abstract public function init();
}