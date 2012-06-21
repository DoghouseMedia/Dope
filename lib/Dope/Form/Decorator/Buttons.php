<?php

require_once 'Dope/Form/Decorator/ContentPane.php';

class Dope_Form_Decorator_Buttons
extends Dope_Form_Decorator_ContentPane
{	
	public function render($content)
	{
		$html = '<div data-dojo-type="dope.layout.Buttons">'
			. '<input type="submit" dojoType="dope.form.Button" label="Save" />'
			. '</div>';
		
		switch ($this->getPlacement()) {
			case self::APPEND:
				return $content . $html;
			default:
			case self::PREPEND:
				return $html . $content;
		}
	}
}