<?php

require_once 'Text.php';

class Dope_Form_Element_Search
extends Dope_Form_Element_Text
{
	public function init()
	{
		parent::init();
		$this->setRegExp('.*?');
	}
}