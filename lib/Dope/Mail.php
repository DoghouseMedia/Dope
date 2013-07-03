<?php

namespace Dope;

class Mail extends \Zend_Mail
{
	/**
	 * View object (instance)
	 * 
	 * @var Zend_View
	 */
	protected $view;
	
	/**
	 * Layout/View object (instance)
	 * 
	 * @var Zend_View
	 */
	protected $layout;
	
	public function __construct($charset = 'utf-8')
	{
		parent::__construct($charset);
		
		$this->view = new \Zend_View();
		$this->view->setScriptPath(APPLICATION_PATH . '/views/emails');
		$this->view->addHelperPath('Dope/View/Helper/', 'Dope_View_Helper');
		
		$this->layout = new \Zend_View();
		$this->layout->setScriptPath(APPLICATION_PATH . '/layouts/scripts/emails');
	}
	
	/**
	 * @return \Dope\Mail
	 */
	public static function factory()
	{
	    return new static();
	}
	
	public function setBodyText($txt, $charset = null, $encoding = \Zend_Mime::ENCODING_QUOTEDPRINTABLE)
	{
		$this->layout->content = $txt;
		
		return parent::setBodyText(
			$this->layout->render('txt.phtml'),
			$charset,
			$encoding
		);
	}
	
	public function setBodyHtml($html, $charset = null, $encoding = \Zend_Mime::ENCODING_QUOTEDPRINTABLE)
	{
		$this->layout->content = $html;
		
		return parent::setBodyHtml(
			$this->layout->render('html.phtml'),
			$charset,
			$encoding
		);
	}
	
	public function setBodyView($viewName, $charset = null, $encoding = \Zend_Mime::ENCODING_QUOTEDPRINTABLE)
	{		
		$html = $this->view->render($viewName);
		$txt = strip_tags($html);
		
		$this->setBodyText($txt, $charset, $encoding);
		$this->setBodyHtml($html, $charset, $encoding);
		
		return $this;
	}
	
	public function setBodyParam($key, $value)
	{
		$this->view->$key = $value;
		return $this;
	}
	
	public function send($transport = null)
	{
		/* 
		 * Replace recipients by debugging values unless in production
		 * @todo This check should probably be encapsulated somewhere else... 
		 */
		if (! \Dope\Env::isProduction()) {
			$this->clearRecipients();

			foreach(\Dope\Config\Helper::getOption('mail.defaults') as $email) {
				$this->addTo($email);
			}
		}
		
		/* Check we have some recipients */
		if (count($this->getRecipients()) == 0) {
			throw new \Exception("Could not determine a valid recipient");
		}

		return parent::send($transport);
	}
	
	public function addTos($emailString)
	{
		foreach(explode(',', $emailString) as $email) {
			$this->addTo(trim($email));
		}
		return $this;
	}
	
	public function addCcs($emailString)
	{
		foreach(explode(',', $emailString) as $email) {
			$this->addCc(trim($email));
		}
		return $this;
	}
	
	public function addBccs($emailString)
	{
		foreach(explode(',', $emailString) as $email) {
			$this->addBcc(trim($email));
		}
		return $this;
	}
}
