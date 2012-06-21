<?php

namespace Dope\Service\LiveDocx;

use Dope\Entity,
	Dope\Config\Helper as Config,
	Dope\Entity\Helper\Date as DateHelper;

class MailMerge extends \Zend_Service_LiveDocx_MailMerge
{
	/**
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $request;
	
	protected $isDebug = false;
	
	public function __construct($options = null)
	{
		parent::__construct($options = null);
		
		$this->setUsername(Config::getOption('service.livedocx.username'));
		$this->setPassword(Config::getOption('service.livedocx.password'));
		
		$this->_wsdl = Config::getOption('service.livedocx.wsdl');
	}
	
	public function getIsDebug()
	{
		return (bool) $this->isDebug;
	}
	
	public function setIsDebug($isDebug)
	{
		$this->isDebug = (bool) $isDebug;
		return $this;
	}
	
	public function setRequest(\Zend_Controller_Request_Abstract $request)
	{
		$this->request = $request;
	}
	
	/**
	 * @return \Zend_Controller_Request_Abstract
	 */
	public function getRequest()
	{
		return $this->request;
	}
	
	public function getFieldValues()
	{
		return $this->_fieldValues;
	}
	
	public function assign($field, $value)
	{
		if ($this->getIsDebug()) {
			echo "$field\n";
			var_dump($value);
			echo "\n\n";
		}
		
		return parent::assign($field, $value);
	}
	
	public function assignRecord(Entity $model, $prefix='')
	{
		/* Assign model's fields */
		foreach($model->getTable()->getColumnNames() as $columnName) {
			$columnDefinition = $model->getTable()->getColumnDefinition($columnName);
			
			switch($columnDefinition['type']) {
				/* For booleans, convert values to 'Yes'/'No' instead 1/0. */ 
				case 'boolean':
					$this->assign($prefix . $columnName, $model->$columnName ? 'Yes' : 'No');
					break;
				/* Dates & Timestamps */
				case 'date':
				case 'timestamp':
					$this->assign($prefix . $columnName, $model->$columnName);
					$this->assign($prefix . $columnName . '_formatted', date('d M Y, H:i', strtotime($model->$columnName)));
					$this->assign($prefix . $columnName . '_formatted_short', date('d M Y', strtotime($model->$columnName)));
					break;
				/* For other values, just assign value. */
				default:
					$this->assign($prefix . $columnName, $model->$columnName);
					break;
			}
		}
		
		/* Assign date (if no date field) */
		if (! in_array('date', $model->getTable()->getColumnNames())) {
			$this->assign($prefix . 'date', date('d M Y'));
		}
		
		/* Assign relations */
		$this->assignRecordRelations($model, $prefix);
		
		/* Extras */
		if ($model instanceof \Dope\Entity\Printable) {
			$model->assignMailMergeExtras($this, $prefix);
		}
		
		return $this;
	}
	
	public function assignRecordRelations(Entity $model, $prefix='')
	{
		$model->refreshRelated();
		
		/*
		 * Assign 
		 * @var $relation Doctrine_Relation
		 */
		foreach($model->getTable()->getRelations() as $referenceName => $relation) {
			if ($model->$referenceName instanceof \Dope\Entity) {
				foreach($model->$referenceName->getTable()->getColumnNames() as $columnName) {
					if (is_object($model->$referenceName->$columnName)) {
						continue;
					}
					$this->assign(
						$prefix . strtolower($referenceName) . '_' . $columnName, 
						$model->$referenceName->$columnName
					);
				}
			}
			elseif ($model->hasRelation($referenceName) AND count($model->$referenceName)) {
				$arrays = array();
				
				/* @var $record Entity */
				foreach($model->$referenceName as $record) {
					if (! $record instanceof \Dope\Entity) continue;

					$array = array();
					foreach($record->getTable()->getColumnNames() as $columnName) {
						/*
						 * Format dates
						 */
						if (preg_match("/^(\d{4})-(\d{2})-(\d{2})/", (string) $record->$columnName, $matches)) {
							$array[$columnName] = DateHelper::format($record->$columnName);
						}
						elseif (substr($columnName, -6) == '_month' AND $record->$columnName) {
							/*
							 * First 3 letters of month
							 * @todo Could be done a lot better
							 */
							$months = Core_Form::getMonths();
							$array[$columnName] = (string) substr($months[
								str_pad($record->$columnName, 2, '0', STR_PAD_LEFT)
							], 0, 3);
						}
						else {
							$array[$columnName] = (string) $record->$columnName;
						}
					}
					
					$arrays[] = $array;
				}
				
				if (! count($arrays)) continue;
				
				try {
					$this->assign($prefix . strtolower($referenceName) . '_count', count($arrays));
					$this->assign($prefix . strtolower($referenceName), $arrays);
				}
				catch (\Zend_Service_LiveDocx_Exception $e) {
					// silence livedocx exception
				}
			}
		}
		
		return $this;
	}
}