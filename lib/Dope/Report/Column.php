<?php

namespace Dope\Report;

abstract class Column
{
    const LABEL = "Label";
    
    protected $sortFields = array();
    protected $alias = '';
    protected $accessor = null;
    protected $totals = array();
    
    public function __construct($alias = '', $accessor = null)
    {
        $this->setAlias($alias);
        $this->setAccessor($accessor);
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
    
    public function getAccessor()
    {
        return $this->accessor;
    }
    
    public function setAccessor($accessor = null)
    {
        $this->accessor = $accessor;
        return $this;
    }
    
    public function getEntityByAccessor(\Dope\Entity $entity)
    {
        $columnEntity = $entity;
        
        if ($this->getAccessor()) {
            foreach (explode('.', $this->getAccessor()) as $accessorPart) {
                $columnEntity = $columnEntity->{$accessorPart};
            }
        }
        
        return $columnEntity;
    }
    
    public function getSortFields()
    {
        return $this->sortFields;
    }
    
    public function setSortFields(array $sortFields = array())
    {
        $this->sortFields = $sortFields;
        return $this;
    }
    
    public function getSort()
    {
        $column = $this;
        return join(',', array_map(function($field) use ($column) {
            if ($column->getAlias()) {
                return $column->getAlias() . '.' . $field;
            } else {
                return $field;
            }
        }, $this->getSortFields()));
    }
    
    public function renderPlain (\Dope\Entity $entity=null, \Zend_View $view)
    {
        return $entity ? $this->render($entity, $view) : '';
    }
    
    abstract public function render(\Dope\Entity $entity=null, \Zend_View $view);

    public function renderTotals(\Zend_View $view)
    {
        $html = array();
        foreach ($this->totals as $key => $value) {
            $html[] = ucfirst($key) . ': ' . $value;
        }

        return join("<br>\n", $html);
    }

    protected function hasTotal($key)
    {
        if (!isset($this->totals[$key])) {
            $this->totals[$key] = 0;
        }
        return $this;
    }

    protected function addTotal($key, $value)
    {
        $this->hasTotal($key);
        $this->totals[$key] += (int) $value;
        return $this;
    }
}