<?php

class Dope_Form_Element_ValidationTextBox
extends Zend_Dojo_Form_Element_ValidationTextBox
{
	public function setRegExp($regexp)
	{
		/* Remove other Regexp validators */
		foreach($this->getValidators() as $name => $validator) {
			if ($validator instanceof Zend_Validate_Regex) {
				$this->removeValidator($name);
			}
		}
		
		/* Add new Regexp validator */
		$this->addValidator(new Zend_Validate_Regex('/' . $regexp . '/i'));
		
		return parent::setRegExp($regexp);
	}
}