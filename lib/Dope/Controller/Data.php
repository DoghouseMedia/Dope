<?php

namespace Dope\Controller;

class Data
{
	const FILTER_RESERVED_PARAMS = true;
	
	/**
	 * Reserved params (filtered from getParams() by default)
	 * 
	 * @var array
	 */
	protected $reservedParams = array(
		'module', 'controller', 'action', // ZF routing
		'format', // ZF context
		'id',
		'dojo_preventCache',
		'tab', 'parent_tab', // DHM tabs
		'sender', //
		'list_count', 'list_start', // DHM/Doctrine limit
		'exclude', // DHM/Doctrine exclude filter
		'sort', 'sort*', '\?sort*',  // DHM/Doctrine sort
		'src_class', 'foreign_id', // DHM legacy relation filtering (probably obsolete)
		//'join', 'join_with', // join,
		'error_handler', //
		'select', //
		'_use_value', // used for some autocompletion
		'_debug' // debug flag
	);
	
	/**
	 * @var array
	 */
	protected $params = array();
	
	public function __construct(array $params = array())
	{
		foreach($params as $key => $value) {
			$this->setParam($key, $value);
		}
	}
	
	public function __get($key)
	{
		return $this->getParam($key);
	}
	
	public function hasParam($key)
	{
		return (bool) isset($this->params[$key]);
	}
	
	public function getParam($key, $filterReservedParams=false)
	{
		if (! $this->hasParam($key)) return false;
		
		$params = $this->params;
		
		if ($filterReservedParams) {
			$params = $this->filterReservedParams($params);
		}
		
		return isset($params[$key]) ? $params[$key] : false;
	}
	
	public function getParams($filterReservedParams=true)
	{
		$params = $this->params;
		
		if ($filterReservedParams) {
			$params = $this->filterReservedParams($params);
		}

		return new \ArrayObject($params);
	}
	
	public function toArray()
	{
		return $this->getParams()->getArrayCopy();
	}
	
	protected function filterReservedParams($params)
	{
		/*
		 * This should probably be written with array_* functions but was unable to get it working...
		 * 
		 * We need to remove all the values who's keys are in reservedParams
		 * 
		 * Jonathan, 2010-09-06
		 */
		$paramKeys = array_keys($params);
		
		for($i=0; $i < count($this->reservedParams); $i++) {
			if (strpos($this->reservedParams[$i], '*')) {
				$pattern = '/^' . str_replace('*', '.+', $this->reservedParams[$i]) . '$/';
				
				foreach($paramKeys as $paramKey) {
					if (preg_match($pattern, $paramKey)) {
						unset($params[$paramKey]);
					}
				}
			}
			elseif (in_array($this->reservedParams[$i], $paramKeys)) {
				unset($params[$this->reservedParams[$i]]);
			}
		}
		
		return $params;
	}
	
	public function setParam($key, $value)
	{
		if (is_string($value) && $value == '*') {
			return;
		}
		
		$this->params[$key] = $value;
	}
	
	public function removeParam($key)
	{
		if ($this->hasParam($key)) {
			unset($this->params[$key]);
		}
		
		return $this;
	}
	
	public function clearParams()
	{
		$this->params = array();
		
		return $this;
	}
	
	protected function _getTab($tabId, \Snowwhite\Entity\User $user)
	{
		return \Dope\Doctrine::getRepository('Snowwhite\Entity\Tab')->findOneBy(array(
			'dom_id' => $tabId,
			'user' => $user
		));
	}
	
	public function save($tabId)
	{
		$user = \Dope\Auth\Service::getUser();
		$tab = $this->_getTab($tabId, $user);
		
		if ($tab) {
			$tab->state_data = $this->getParams(true);
			$tab->save();
		}
		
		return $this;
	}
	
	public function load($tabId, $parentTabId=false)
	{
		$user = \Dope\Auth\Service::getUser();
		$tab = $this->_getTab($tabId, $user);
		$tabForParams = $tab;
		
		/* If there is parentTab, copy state_data to new tab */
		if ($parentTabId) {
			$parentTab = \Dope\Doctrine::getRepository('Snowwhite\Entity\Tab')->findOneBy(array(
				'dom_id' => $parentTabId,
				'user' => $user
			));
			
			if ($parentTab instanceof \Snowwhite\Entity\Tab AND $parentTab->state_data instanceof \ArrayObject) {
				if ($tab instanceof \Snowwhite\Entity\Tab) {
					$tab->cloneStateData($parentTab);
					$tabForParams = $tab;
				}
				else {
					$tabForParams = $parentTab;
				}
			}
		}
			
		if ($tabForParams instanceof \Snowwhite\Entity\Tab) {
			if($tabForParams->state_data instanceof \ArrayObject) {
				foreach($tabForParams->state_data->getArrayCopy() as $key => $value) {
					if (!isset($this->params[$key])) {
						$this->params[$key] = $value;
					}
				}
			}
		}
		
		return $this;
	}
}