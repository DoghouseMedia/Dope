<?php

class Dope_View_Helper_Table extends Zend_View_Helper_Abstract
{
	protected $rows = array();
	protected $tableAttributes = array();
	
	public function table(Traversable $rows)
	{
		$this->setRows($rows);
		return $this;
	}
	
	public function getRows()
	{
		return $this->rows;
	}
	
	public function setRows(Traversable $rows)
	{
		$this->rows = $rows;
		return $this;
	}
	
	public function getAttributes()
	{
		return $this->tableAttributes;
	}
	
	public function getAttribute($name)
	{
		return isset($this->tableAttributes[$name]) ?
			$this->tableAttributes[$name] :
			'';
	}
	
	public function setAttribute($name, $value)
	{
		$this->tableAttributes[$name] = $value;
		return $this;
	}
	
	public function addClass($className)
	{
		$classNames = explode(' ', $this->getAttribute('class'));
		array_push($classNames, $className);
		$this->setAttribute('class', join(' ', $classNames));
		return $this;
	}
	
	public function __toString()
	{
		try {
			$html = '<table';
			foreach ($this->getAttributes() as $name => $val) {
				$html .= ' ' . $name . '=' . '"' . $val . '"';
			}
			$html .= '>';
			
			foreach($this->getRows() as $key => $values) {
				$html .= '<tr>';
				if (! is_array($values)) {
					$values = array($values);
				}
				if (! ctype_digit($key)) {
					$html .= '<th>';
					$html .= $this->formatKey($key);
					$html .= '</th>';
				}
				foreach($values as $value) {
					$html .= '<td>';
					$html .= $this->formatValue($key, $value);
					$html .= '</td>';
				}
				$html .= '</tr>';
			}
			
			$html .= '</table>';
		}
		catch(Exception $e) {
			$html = '<p class="error">' . $e->getMessage() . '</p>';
		}
		
		return $html;
	}
	
	protected function formatKey($key)
	{	
		if ($this->testIsForeignKey($key)) {
			$formattedKey = $this->getForeignKeyRelationAlias($key);
		}
		else {
			$formattedKey = $this->view->escape(str_replace('_', ' ', $key));
		}
		
		return ucfirst($formattedKey);
	}
	
	protected function formatValue($key, $value)
	{
// 		if ($this->testIsForeignKey($key)) {
// 			return $this->view
// 				->modelUrl($this->getRows()->{$this->getForeignKeyRelationAlias($key)})
// 				->toHtml();
// 		}
// 		elseif ($this->testIsBool($key)) {
// 			return $value ? 'Yes' : 'No';
// 		}
// 		elseif ($this->testIsCurrency($key)) {
// 			return $this->view->currencyFormatter($value);
// 		}
// 		elseif ($key == 'id') {
// 			return (int) $value;
// 		}
// 		else {
// 			return $this->view->fieldFormatter($value);
// 		}
// 		return $key . ': ' . (
// 			is_object($value) ?
// 				get_class($value) :
// 				gettype($value)		
// 		);
		if ($this->testIsEntity($key)) {
			return $this->view
				->modelUrl($this->getRows()->{$key})
				->toHtml();
		}
		elseif ($this->testIsCollection($value)) {
			return '#' . $value->count();
		}
		else {
			return $this->view->fieldFormatter($value);
		}
	}
	
	protected function testIsEntity($key)
	{
		if (! isset($this->getRows()->{$key})) {
			return false;
		}
		
		return ($this->getRows()->{$key} instanceof \Dope\Entity);
	}
	
	protected function testIsCollection($value)
	{
		return (bool) ($value instanceof \Doctrine\Orm\PersistentCollection);
	}

	protected function testIsForeignKey($key)
	{
		throw new \Exception('fk_ terminology is outdated and no longer works.  Fix it.');
		return (bool) preg_match('/^fk_.*_id$/', $key);
	}
	
	protected function testIsBool($key)
	{
		if (! $this->getRows() instanceof Core_Model) {
			return false;
		}
		
		$colDef = $this->getRows()->getTable()->getColumnDefinition($key);
		
		return ($colDef['type'] == 'boolean');
	}
	
	protected function testIsCurrency($key)
	{
		if (! $this->getRows() instanceof Core_Model) {
			return false;
		}
	
		$form = $this->getRows()->getTable()->getForm();
		
		if (!$form OR !$form->hasElement($key)) {
			return false;
		}
		
		$formElement = $form->getElement($key);
		
		return (bool) ($formElement instanceof Zend_Dojo_Form_Element_CurrencyTextBox);
	}
	
	protected function getForeignKeyRelationAlias($key)
	{
		return $this->getRows()->getTable()->getForeignKeyRelationAlias($key);
	}
}
