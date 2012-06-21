<?php

require_once 'Email.php';

class Dope_Form_Element_EmailMultiple
extends Dope_Form_Element_Email
{
	public function init()
	{
		parent::init();
		$this->setRegExp('^' . static::REGEXP . '(,' . static::REGEXP . ')*$');
	}
}