<?php

namespace Dope\Printer;

use Dope\Config\Helper as Config;

class Template
{
	public function __construct($path)
	{
		$this->adapter = new \Zend_Service_LiveDocx_MailMerge();
		$this->adapter
			->setUsername(Config::getOption('service.livedocx.username'))
			->setPassword(Config::getOption('service.livedocx.password'))
			->setWsdl(Config::getOption('service.livedocx.wsdl'))
			->setLocalTemplate($path);
	}
	
	public function assign($key, $val)
	{
		return $this->adapter->assign($key, $val);
	}
	
	public function toPdf()
	{
		$this->adapter->createDocument();
		return $this->adapter->retrieveDocument('pdf');
	}
	
	public function toDocx()
	{
		$this->adapter->createDocument();
		return $this->adapter->retrieveDocument('docx');
	}
}