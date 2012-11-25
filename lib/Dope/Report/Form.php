<?php

namespace Dope\Report;

class Form extends \Dope\Form\Entity\Search
{
    const DATE_FORMAT = 'Y-m-d';
    
    public function init()
    {
        parent::init();
    
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('class' => 'main')),
            /*
             * @todo Can we use SearchFilters to do something useful for reports?
             */
            'SearchFilters',
            'ReportForm'
        ));
        
        $this->setMethod('GET');
    }
    
    public function render(Zend_View_Interface $view = null)
    {
        $this->addSubmitButton();
        
        $this->populate();
        
        return parent::render($view);
    }
    
    protected function _addDateRangeOrDateCurrent()
    {
        $this->_addDateRange();
        $this->_addDateCurrent();
        $this->_addMultipleSubmitReset(array('date_current', 'date_range'));
        $this->addElement('hidden', 'use_range');
    
        return $this;
    }
    
    protected function _addDate($name='date', $label='Date to report on (format dd/mm/yyyy):', $value=null)
    {
        $this->addElement('Date', $name, array(
            'label'   => $label,
            'datePattern' => 'dd/MM/yyyy',
            'value' => $value,
            'default' => $value
        ));
    
        return $this; //chainable
    }
    
    public function _addDateCurrent($name='date', $label='Date to report on (format dd/mm/yyyy):')
    {
        return $this->_addDate($name, $label, date(static::DATE_FORMAT));
    }
    
    protected function _addState()
    {
        $this->addElement('ComboBox', 'state', array(
            'label' => 'State:',
            'multiOptions' => $this->getStates()
            //'id' => ''
        ));
    
        return $this; //chainable
    }
    
    protected function _addMultipleSubmitReset(array $arrayOfNameSuffixes)
    {
        foreach ($arrayOfNameSuffixes as $nameSuffix) {
            $this->_addSubmit('submit_' . $nameSuffix);
            $this->_addReset('reset_' . $nameSuffix);
        }
    
        return $this; //chainable
    }

    protected function _addAgency()
    {
        return $this->addElement('StoreBox', 'agency', array(
            'label' => 'Agency:'
        ));
    }
    
    protected function _addCategory()
    {
        return $this->addElement('StoreBox', 'category', array(
            'label' => 'Category:'
        ));
    }
    
    protected function _addConsultant($fieldName='user', $label="Consultant:")
    {
        /**
         * @todo Fix
         */
        return $this->addElement('FilteringSelect', $fieldName, array(
            'label'        => $label,
            'storeId' => 'userStore',
            'storeType'=> 'dojo.data.ItemFileReadStore',
            'storeParams' => array('url' => '/user/autocomplete/show_reports/1/format/dojo'),
            'autoComplete'   => 'false',
            'hasDownArrow'   => 'true',
            'value' => null
        ));
    }
    
    protected function _addStatsConsultant()
    {
        /**
         * @todo Fix
         */
        return $this->addElement('FilteringSelect', 'user', array(
            'label'        => 'Consultant:',
            'storeId' => 'userStore',
            'storeType'=> 'dojo.data.ItemFileReadStore',
            'storeParams' => array('url' => '/user/autocomplete/show_stats/1/format/dojo'),
            'autoComplete'   => 'false',
            'hasDownArrow'   => 'true',
            'value' => null
        ));
    }
    
    protected function _addOffice()
    {
        return $this->addElement('ComboBox', 'office', array(
            'label'        => 'Office:',
            'multiOptions' => $this->getOffices()
            //'id' => ''
        ));
    }
}