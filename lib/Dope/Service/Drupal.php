<?php

namespace Dope\Service;

class Drupal extends \Zend_Service_Abstract
{
	/**
	 * Client
	 *
	 * @var \Dope\Json\Client
	 */
	protected $client;
	
	protected $apiKey;
	protected $apiDomain;
	protected $apiUsername;
	protected $apiPassword;
	protected $apiSid;
	
	protected $mailDefaults = array();
	
	protected $isConnected = false;
	
	public function __construct($uri, $apiKey, $apiDomain, $username, $password, array $mailDefaults)
	{
		$this->client = new \Dope\Json\Client($uri);
		$this->client->setSkipSystemLookup(true);
		
		$this->apiKey = $apiKey;
		$this->apiDomain = $apiDomain;
		$this->apiUsername = $username;
		$this->apiPassword = $password;
		
		$this->mailDefaults = $mailDefaults;
	}
	
	public function notify($subject, $message)
	{
		$mail = new \Zend_Mail();
		
		foreach ($this->mailDefaults as $email) {
			$mail->addTo($email);
		}
		
		$mail->setSubject($subject);
		$mail->setBodyText($message);
			
		return $mail->send();
	}
	
	public function connect($force=false)
	{
		if (!$this->isConnected OR $force) {
			/* Get session id */
			$response = $this->client->call('system.connect');
			$this->apiSid = $response['sessid'];
			
			if ($this->apiUsername AND $this->apiPassword) {
				$params = $this->prepareParamsWithHash('user.login', array(
					'username' => $this->apiUsername,
					'password' => $this->apiPassword
				));
				
				/* Login */
				$response = $this->client->call('user.login', $params);
				$this->apiSid = $response['sessid'];
			}
		}
		
		$this->isConnected = true;
	}
	
	public function __destruct()
	{
		//$this->call('user.logout', array());
	}
	
	public function call($method, $params)
	{
		$this->connect();
		
		/* Create secure hash using your api key. */
		$params = $this->prepareParamsWithHash($method, $params);
		
		return $this->client->call($method, $params);
	}

	protected function prepareParamsWithHash($method, array $params=array())
	{
		$nonce = uniqid(rand(123,999), true);
		$timestamp = (string) time();
		
		/* Prepend auth to beginning of params */
		$params['hash'] = hash_hmac('sha256', $timestamp .';'. $this->apiDomain .';'. $nonce .';'. $method, $this->apiKey);
		$params['domain_name'] = $this->apiDomain;
		$params['domain_time_stamp'] = $timestamp;
		$params['nonce'] = $nonce;
		$params['sessid'] = $this->apiSid;
		
		return $params;
	}
}