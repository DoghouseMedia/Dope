<?php

namespace Dope\Form\Entity;

class Xhr extends \Dope\Form\Entity
{
	public function init()
	{
		parent::init();
		
		$this->setDecorators(array(
			'FormElements',
			'XhrForm'
		));
	}
	
	public function postConfigure()
	{
		parent::postConfigure();
		$this->addSubmitButton();
	}
}