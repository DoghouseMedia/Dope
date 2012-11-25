<?php

namespace Dope\Report;

abstract class Column
{
    const LABEL = "Label";
    
    protected $sortFields = array();
    protected $alias = '';
    protected $accessor = null;
    
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
            return $column->getAlias() . '.' . $field;
        }, $this->getSortFields()));
    }
    
    public function renderPlain (\Dope\Entity $entity, \Zend_View $view)
    {
        return $this->render($entity, $view);
    }
    
    abstract public function render(\Dope\Entity $entity, \Zend_View $view);
}