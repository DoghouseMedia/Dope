<?php

class Dope_View_Helper_FieldFormatter extends Zend_View_Helper_Abstract
{
	public function fieldFormatter($string)
	{
		return $this->applyFilters($string);
	}
	
	protected function applyFilters($string)
	{
		/*
		 * Email
		 * 
		 * Convert email addresses to links to mailer
		 * Note: This test must be followed by the HTML one. See below for more info.
		 */
		$string = preg_replace_callback('/([^@\s<>]+@[^@\s<>]+)/', function($matches) {
			/* We need the view object but can't access $this from the current scope :( */
			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
			
			$email = $view->escape($matches[1]);
			
			return '<a data-dojo-type="snowwhite.button.Email" href="mailto:' . $email . '">' . $email . '</a>';
		}, $string);
		
		/*
		 * HTML
		 * 
		 * Note: It's vital this test comes after the email one, so that
		 * this test returns a tidy email link.  If you move this before email test,
		 * the links will be escaped by the last default test and won't work.
		 */
		if (strlen($string) != strlen(strip_tags($string))) {
			return $this->view->tidy($string);
		}
		
		/* Color */
		if (preg_match('/#[a-z0-9]{6}/mis', $string)) {
			return $this->view->colorFormatter($string);
		}
		
		/* DateTime */
		if (preg_match('/^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$/', $string)) {
			return $this->view->dateFormatter($string);
		}
		
		/* Default */
		return nl2br($this->view->escape($string));
	}
}
