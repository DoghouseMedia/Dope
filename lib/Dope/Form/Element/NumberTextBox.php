<?php

class Dope_Form_Element_NumberTextBox
extends Zend_Dojo_Form_Element_NumberTextBox
{
	public function setRegExp($regexp)
	{
		// Remove other Regexp validators
		foreach($this->getValidators() as $validator) {
			if ($validator instanceof Zend_Validate_Regex) {
				$this->removeValidator($validator->getName());
			}
		}
		
		// Add new Regexp validator
		$this->addValidator(new Zend_Validate_Regex('/' . $regexp . '/i'));
		
		return parent::setRegExp($regexp);
	}
}