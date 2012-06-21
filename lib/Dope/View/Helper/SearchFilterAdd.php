<?php

class Dope_View_Helper_SearchFilterAdd extends Zend_View_Helper_HtmlElement
{
	public function searchFilterAdd($name, $value, $dijitParams, $attribs, $options)
	{
		$return = '
			<select data-dojo-type="dope.search.form.FilterAdd">
				<option>Add filter</option>
		';
		
		foreach($dijitParams['searchFilters'] as $filter) {
			$return .= '	
				<option value="' . $filter->getKey() . '-' . $filter->getType() .'-' . $this->view->modelAlias() . '"
					data-key="' . $filter->getKey() . '"
					data-type="' . $filter->getType() . '"
					data-title="' . $filter->getTitle() . '"
					data-modelAlias="' . $this->view->modelAlias() . '">
					' . $filter->getTitle() . '
				</option>
			';
		}
			
		$return .= '
			</select>
		';
		
		return $return;
	}
}
