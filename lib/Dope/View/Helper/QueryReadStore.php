<?php

class Dope_View_Helper_QueryReadStore extends Zend_View_Helper_Abstract
{
	/**
	 * Return a Dojo QueryReadStore
	 * 
	 * @return int
	 */
	public function queryReadStore()
	{
		$modelAlias = $this->view->modelAlias();
		
		if (!$modelAlias) { 
			return false;
		}
		
		$html = '
			<span 
				dojoType="dojox.data.QueryReadStore" 
				jsId="'.$modelAlias.'Store"
				url="' .$this->view->url(	
					array(
						'controller' => $modelAlias,
						'action' => 'list-data',
						'format' => 'ajax',
						),
					'default',
					true
				).'">
			</span>
		';
		
		return $html;
	}
}
