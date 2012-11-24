<?php

class Dope_View_Helper_ModelUrl extends Dope_View_Helper_Url
{
	/**
	 * $model \Dope\Entity
	 */
	protected $model;
	
	protected $label;
	
	/**
	 * 
	 */
	public function modelUrl(\Dope\Entity $model=null, $label=null)
	{
		$this->model = $model;
		$this->label = $label;
		
		return $this;
	}
	
	protected function getUrl()
	{
		if (! $this->model instanceof \Dope\Entity) {
		    return '';
		}
		
		return $this->url(array(
			'controller' => $this->model->getEntityKey(),
			'action' => $this->model->id
		), null, true);
	}
	
	public function toHtml($dojoType='dope.link.NewTab')
	{
		if (! $this->model instanceof \Dope\Entity) {
		    return '';
		}
		
		$title = (string) $this->model;
		$label = $this->label ?: (string) $this->model;
		
		return '<a dojoType="' . $dojoType 
			. '" title="' . $this->view->escape($title) 
			. '" href="' . $this->getUrl() . '">'
			. $this->view->escape($label) . '</a>';
	}
	
	public function __toString()
	{
		return $this->getUrl();
	}
}