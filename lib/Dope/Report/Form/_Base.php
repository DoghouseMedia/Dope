<?php

namespace Dope\Report\Form;

class _Base extends \Dope\Form\_Base
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
            'default' => $value,
            //'decorators' => $this->_getDecorators()
        ));
    
        return $this; //chainable
    }
    
    protected function _addDateCurrent($name='date', $label='Date to report on (format dd/mm/yyyy):')
    {
        $this->_addDate($name, $label, date(static::DATE_FORMAT));
    
        return $this; //chainable
    }
    
    protected function _addState()
    {
        $this->addElement('ComboBox', 'state', array(
            'label'        => 'State:',
            'multiOptions' => $this->getStates(),
            'id' => '',
            //'decorators' => $this->_getDecorators()
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
        foreach($arrayOfNameSuffixes as $nameSuffix) {
            $this->_addSubmit('submit_' . $nameSuffix);
            $this->_addReset('reset_' . $nameSuffix);
        }
    
        return $this; //chainable
    }
    
    protected function _addSubmit($name='submit')
    {
        $this->addElement('submitButton', $name, array(
            'required'    => false,
            'ignore'      => true,
            'label'       => 'Submit',
            //'decorators' => $this->_getButtonDecorators()
        ));
    
        $this->addElement('hidden', '_submit', array(
            'value'    => '_submit',
            //'decorators' => $this->_getButtonDecorators()
        ));
    
        return $this; //chainable
    }
    
    protected function _addReset($name='reset')
    {
        $this->addElement('button', $name, array(
                'label' => 'Reset',
                //'decorators' => $this->_getButtonDecorators(),
                'class' => 'reset'
        ));
    
        return $this; //chainable
    }
    
    protected function _addAgency()
    {
        $this->addElement('FilteringSelect', 'agency', array(
            'label'        => 'Agency:',
            'storeId' => 'userStore',
            'storeType'=> 'dojo.data.ItemFileReadStore',
            'storeParams' => array('url' => '/agency/autocomplete/format/dojo'),
            'autoComplete'   => 'false',
            'hasDownArrow'   => 'true',
            //'decorators' => $this->_getDecorators(),
            'value' => \Dope\Auth\Service::hasUser() ? \Dope\Auth\Service::getUser()->id : null
        ));
    
        return $this; //chainable
    }
    
    protected function _addCategory()
    {
        $this->addElement('FilteringSelect', 'category', array(
            'label'        => 'Category:',
            'storeId' => 'categoryStore',
            'storeType'=> 'dojo.data.ItemFileReadStore',
            'storeParams' => array( 'url' => '/category/autocomplete/format/dojo'),
            'autoComplete'   => 'false',
            'hasDownArrow'   => 'true',
            //'decorators' => $this->_getDecorators()
        ));
    
        return $this; //chainable
    }
    
    protected function _addConsultant($fieldName='user', $label="Consultant:")
    {
        $this->addElement('FilteringSelect', $fieldName, array(
            'label'        => $label,
            'storeId' => 'userStore',
            'storeType'=> 'dojo.data.ItemFileReadStore',
            'storeParams' => array('url' => '/user/autocomplete/show_reports/1/format/dojo'),
            'autoComplete'   => 'false',
            'hasDownArrow'   => 'true',
            //'decorators' => $this->_getDecorators(),
            'value' => null
        ));
    
        return $this; //chainable
    }
    
    protected function _addStatsConsultant()
    {
        $this->addElement('FilteringSelect', 'user', array(
            'label'        => 'Consultant:',
            'storeId' => 'userStore',
            'storeType'=> 'dojo.data.ItemFileReadStore',
            'storeParams' => array('url' => '/user/autocomplete/show_stats/1/format/dojo'),
            'autoComplete'   => 'false',
            'hasDownArrow'   => 'true',
            //'decorators' => $this->_getDecorators(),
            'value' => null
        ));
    
        return $this; //chainable
    }
    
    protected function _addOffice()
    {
        $this->addElement('ComboBox', 'office', array(
            'label'        => 'Office:',
            'multiOptions' => $this->getOffices(),
            'id' => '',
            //'decorators' => $this->_getDecorators()
        ));
    
        return $this; //chainable
    }
    
    protected function _addClient()
    {
        $this->addElement('FilteringSelect', 'client', array(
            'label'        => 'Client:',
            'storeId' => 'clientStore',
            'storeType'=> 'dojo.data.ItemFileReadStore',
            'storeParams' => array('url' => '/client/autocomplete/format/dojo'),
            'autoComplete'   => 'false',
            'hasDownArrow'   => 'true',
            //'decorators' => $this->_getDecorators()
        ));
    
        return $this; //chainable
    }
}