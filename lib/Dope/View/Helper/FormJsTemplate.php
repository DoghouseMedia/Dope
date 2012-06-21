<?php

class Dope_View_Helper_FormJsTemplate extends Zend_View_Helper_Abstract
{
	public function formJsTemplate($id = null, $content = '', array $params = array(), $attribs = array())
	{
		$form = $params['form'];
		
		$html  = '<script type="text/html" id="' . $id . '" class="template">';
		$html .= $form->render();
		$html .= '</script>';
		
		return $html;
	}
}