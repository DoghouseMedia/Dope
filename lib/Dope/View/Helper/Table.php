<?php

class Dope_View_Helper_Table extends Zend_View_Helper_Abstract
{
	protected $rows = array();
	protected $headers = array();
	protected $caption = null;
	protected $attributes = array();
	
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
	
	public function getHeaders()
	{
        return $this->headers;
	}
	
	public function setHeaders(Traversable $headers)
	{
        $this->headers = $headers;
        return $this;
	}
	
	public function getAttributes()
	{
		return $this->attributes;
	}
	
	public function getAttribute($name)
	{
		return isset($this->attributes[$name])
			? $this->attributes[$name]
			: '';
	}
	
	public function setAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
		return $this;
	}
	
	public function getCaption()
	{
	    return $this->caption;
	}
	
	public function setCaption($caption)
	{
	    $this->caption = $caption;
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
			
			if ($this->getCaption()) {
			    $html .= '<caption>';
			    $html .= $this->getCaption();
			    $html .= '</caption>';
			}
			
			if (count($this->getHeaders()) > 0) {
    			$html .= '<thead>';
    			$html .= '<tr>';
    			foreach ($this->getHeaders() as $value) {
    			    $html .= '<th>';
    			    $html .= $value;
    			    $html .= '</th>';
    			}
    			$html .= '</tr>';
    			$html .= '<thead>';
			}
			
			$html .= '<tbody>';
			foreach ($this->getRows() as $key => $values) {
				$html .= '<tr>';
				if (! is_array($values)) {
					$values = array($values);
				}
				if (!is_int($key) AND !ctype_digit($key)) {
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
			$html .= '</tbody>';
			$html .= '</table>';
		}
		catch(Exception $e) {
			$html = '<p class="error">' . $e->getMessage() . '</p>';
		}
		
		return $html;
	}
	
	protected function formatKey($key)
	{	
		return ucfirst(
		    $this->view->escape(str_replace('_', ' ', $key))
		);
	}
	
	protected function formatValue($key, $value)
	{
		if ($this->testIsEntity($key)) {
			return $this->view
				->modelUrl($this->getRows()->{$key})
				->toHtml();
		}
		elseif ($this->testIsCollection($value)) {
			return '#' . $value->count();
		}
		elseif ($this->testIsDateTime($value)) {
			return $this->view->dateFormatter($value);
		}
 		elseif ($this->testIsJSON($value)) {
 		    return $this->formatJSON($value);
 		}
// 		elseif ($this->testIsCurrency($key)) {
// 		    return $this->view->currencyFormatter($value);
// 		}
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
	
	protected function testIsBool($key)
	{
	    throw new \Exception("needs refactoring");
	    
		if (! $this->getRows() instanceof Core_Model) {
			return false;
		}
		
		$colDef = $this->getRows()->getTable()->getColumnDefinition($key);
		
		return ($colDef['type'] == 'boolean');
	}
	
	protected function testIsCurrency($key)
	{
	    throw new \Exception("needs refactoring");
	    
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
	
	protected function testIsDateTime($value)
	{
		if (! is_object($value)) {
			return false;
		}
		
		return ($value instanceof \DateTime);
	}
	
	protected function testIsJSON($value)
	{
		if (! is_string($value)) {
			return false;
		}
		
	    $decoded = json_decode($value);
	    return (isset($decoded));	    
	}
	
	protected function formatJSON($value)
	{
	    $pattern = array(',"', '{', '}');
	    $replacement = array(",\n\t\"", "{\n\t", "\n}");	    	   
	    return '<pre>' . print_r(json_decode($value), true) . '</pre>';  
	}
}
