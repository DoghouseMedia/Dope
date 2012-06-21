<?php

class Dope_Form_Element_SearchHelp
extends Zend_Form_Element_Xhtml
{
	public $helper = 'SmartHtml';
	
	public function init()
	{
		$this->setValue(
			$this->getView()->partial('tooltip.phtml',array(
				'type' => 'help',
				'content' =>
					'<h3>Available search commands</h3>'
					. $this->getView()->table(new ArrayObject(array(
						'+' => 'Force a term to appear',
						'-' => 'Exclude a term',
						'"..."' => 'Bunny ears forces an exact match on a group of words'
					)))
					. '<p>You can group these commands any way you want.<p>'
			))
		);
		
		parent::init();
	}
}
