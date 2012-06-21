<?php

namespace Dope\Form\Entity;

class Quick extends \Dope\Form\Entity
{
	public function init()
	{
		parent::init();
		
		$this->setDecorators(array(
			'FormElements',
			'QuickForm'
		));
	}
}