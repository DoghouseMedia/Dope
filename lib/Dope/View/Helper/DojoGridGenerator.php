<?php

class Dope_View_Helper_DojoGridGenerator extends Zend_View_Helper_Abstract
{
	/**
	 * Return a Dojo Grid. 
	 * 
	 * TODO: this is a shitty, shitty hack and when Zend gets around to writing their own grid view helper, use that instead. As of 1.8 I don't think one exists. 
	 * 
	 * @return int
	 */
	public function dojoGridGenerator($fields, $query=null, $style = '', array $extraParams=array())
	{
		$modelAlias = $this->view->modelAlias();

		if (!$modelAlias || !$fields) {
			return false;
		}

		$tableAttributes = array_merge(array(
			'query' => '{id: \'*\'}',
			'dojoType' => 'dojox.grid.DataGrid',
			'class' => $modelAlias . 'Grid', 
			'style' => $style,
			'rowsPerPage' => '50',
			'clientSort' => 'false',
			'rowHeight' => '20px',
			'delayScroll' => 'true',
			'selectionMode' => 'single',
			'noDataMessage' => 'No results'
		), $extraParams);
		
		if ($query) {
			$tableAttributes['store'] = $modelAlias . 'Store';
			$tableAttributes['query'] = $query;
		}
		
		$html = '
			<table';
		
		foreach($tableAttributes as $k => $v) {
			$html .= ' ' . $k . '="' . $v . '"';
		}
		
		$html .= '>';

		$html .= '
				<thead>
				<tr>
		';

		foreach($fields as $field) {
			$_attributes = array();

			foreach($field['options'] as $k => $v) {
				$_attributes[] = $k.'="'.$v.'"';
			}

			$html .= '
						<th ' . join(' ', $_attributes) . '>
							' . $field['heading'] . '
						</th>
			';
		}

		$html .= '
					</tr>
				</thead>
			</table>
		';

		return $html;
	}
}
