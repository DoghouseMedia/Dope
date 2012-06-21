<?php

namespace Dope\Service\Drupal;

use Dope\Service\Drupal as Service;

class Node
{
	public $name;
	public $title;
	public $type;
	public $format;
	public $body;
	
	protected $_service;
	
	public function __construct(Service $service, $nid = null)
	{
		$this->_service = $service;
		
		if (is_int($nid) OR is_numeric($nid)) {
			$nodeArray = $this->_service->call('node.get', array(
				'nid' => (int) $nid
			));
			
			$this->fromArray($nodeArray);
		}
	}
	
	public function fromArray(array $array)
	{
		foreach ($array as $key => $val) {
			$this->{$key} = $val;
		}
		
		return true;
	}
	
	public function toArray()
	{
		return get_object_vars($this);
	}
	
	public function getRequiredFields()
	{
		return array('nid', 'uid', 'name', 'type', 'format', 'title', 'body');
	}
	
	public function isValid()
	{
		foreach ($this->getRequiredFields() as $fieldName) {
			if ( ! isset($this->$fieldNam) OR empty($this->$fieldName)) {
				throw new \Exception("Missing field $fieldName");
			}
		}
		
		return true;
	}
	
	public function save()
	{
		return $this->_service->call('node.save', array(
			'node' => $this->toArray()
		));
	}
	
	public function delete()
	{
		return $this->_service->call('node.delete', array(
			'nid' => (int) $this->nid
		));
	}
}