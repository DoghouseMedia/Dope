<?php

class Dope_View_Helper_SearchFilters extends Zend_View_Helper_Abstract
{
	public function searchFilters()
	{
		return '
			<div data-dojo-type="dope.search.form.Filters"></div>
			<div data-dojo-type="dope.search.form.FilterValues">
				' . json_encode($this->view->data ? $this->view->data->getParams() : null) . '
			</div>
		';
	}
}
