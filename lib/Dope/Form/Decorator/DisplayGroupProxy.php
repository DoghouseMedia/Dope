<?php

require_once 'Dope/Form/Decorator/ContentPane.php';

class Dope_Form_Decorator_DisplayGroupProxy
extends Dope_Form_Decorator_ContentPane
{	
	public function render($content)
	{
		$proxyEntity = $this->getOption('foreignDisplayGroup')->getForm()->getEntity()->getEntityKey();
		
		$html = '<div'
			. ' data-dojo-type="dope.form.group.proxy.Container"' 
			. ' data-dojo-props="proxyEntity:\'' . $this->getOption($proxyEntity) . '\'">'
			. $content
			. '</div>';
		
		return $html;
	}
}