<?php

namespace Dope\Service\ResumeParser;

abstract class Client extends \Zend_Soap_Client
{
	/* Abstract methods */
	
	public abstract function processResumePath($path);
	public abstract function processResumeText($data);
	
	public abstract function getLogin();
	public abstract function getPassword();
	public abstract function getWsdlUrl();
	
	/* Public methods */
	
	public function __construct($wsdl = null, $options = null)
	{
		parent::__construct($wsdl, $options);
		$this->setDefaults($wsdl, $options);
	}
	
	public function setOptions(array $options = array())
	{
		return parent::setOptions(array_merge(
			$this->getDefaultOptions(),
			$options
		));
	}
	
	/* Protected custom methods */
	
	protected function getDefaultOptions()
	{
		return array(
			/* Though deflate is faster, we want the CRC functionality of GZIP */
			'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
			'soap_version' => SOAP_1_1
		);
	}
	
	protected function setDefaults($wsdl = null, $options = null)
	{
		if (is_null($wsdl)) {
			$this->setWsdl($this->getWsdlUrl());
		}
		
		$this->setOptions((array) $options);
	}
}