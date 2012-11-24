<?php

namespace Dope\Report\Form;

class _Base extends \Dope\Form\Entity\Search
{
    const DATE_FORMAT = 'Y-m-d';
    
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
        $this->addElement('DateTextBox', $name, array(
            'label'   => $label,
            'invalidMessage' => 'Invalid date specified.',
            'datePattern' => 'dd/MM/yyyy',
            'value' => $value,
            'default' => $value
        ));
    
        return $this; //chainable
    }
    
    protected function _addDateCurrent($name='date', $label='Date to report on (format dd/mm/yyyy):')
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
    
    protected function _addDateRange()
    {
        $this->_addDateCurrent('from_date', 'From date:');
        $this->_addDateCurrent('to_date', 'To date:');
    
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
    
    protected function _addSubmit($name='submit')
    {
        $this->addElement('submitButton', $name, array(
            'required' => false,
            'ignore' => true,
            'label' => 'Submit'
        ));
    
        $this->addElement('hidden', '_submit', array(
            'value' => '_submit'
        ));
    
        return $this; //chainable
    }
    
    protected function _addReset($name='reset')
    {
        return $this->addElement('button', $name, array(
                'label' => 'Reset',
                'class' => 'reset'
        ));
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
    
    protected function _addClient()
    {
        return $this->addElement('StoreBox', 'client', array(
            'label' => 'Client:'
        ));
    }
}