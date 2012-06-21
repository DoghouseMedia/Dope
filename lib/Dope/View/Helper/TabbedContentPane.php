<?php

class Dope_View_Helper_TabbedContentPane extends Zend_View_Helper_Abstract
{
	public function tabbedContentPane($url, array $params, $title=null)
	{
		return $this->view->contentPane('', array(
			'title' => $title ?: ucfirst(str_replace('/', '', $url)),
			'href' => $url . '?' . http_build_query($params)
        ));
	}
}
