<?php

require_once 'Dope/Form/Decorator/ContentPane.php';

class Dope_Form_Decorator_Quicklinks
extends Dope_Form_Decorator_ContentPane
{	
	public function render($content)
	{
		$html = '<div data-dojo-type="dope.layout.Quicklinks" data-dojo-props="region: \'left\'"></div>';
		
		switch ($this->getPlacement()) {
			case self::APPEND:
				return $content . $html;
			default:
			case self::PREPEND:
				return $html . $content;
		}
	}
}