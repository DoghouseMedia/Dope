<?php

namespace Dope\Service;

use Zend_Config as Config;

class Drupal
{
	/**
	 * Client
	 *
	 * @var \Zend_Http_Client
	 */
	protected $client;
	
	/**
	 * Config
	 * 
	 * @var \Zend_Config
	 */
	protected $config;
	
	/**
	 * Mail defaults
	 * 
	 * @var array of email addresses to notify
	 */
	protected $mailDefaults = array();
	
	/**
	 * Drupal Login Response
	 * 
	 * @var stdClass
	 */
	protected $loginResponse;
	
	/**
	 * Drupal Token Response
	 *
	 * @var stdClass
	 */
	protected $tokenResponse;
	
	/**
	 * Boolean flag to control debugging
	 * @var boolean
	 */
	protected $debug = false;
	
	/**
	 * Constructor
	 * 
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	
	/**
	 * @param boolean $debug
	 * @return boolean
	 */
	public function isDebug($debug = null)
	{
		if (is_bool($debug)) {
			$this->debug = $debug;
		}
		
		return $this->debug;
	}
	
	/**
	 * Login
	 * 
	 * This method is stupid: it assumes that login worked.
	 * If login is incorrect you'll learn about it on your next call.
	 * 
	 * @return \Dope\Service\Drupal
	 */
	public function login()
	{
		if (! $this->loginResponse) {
			$this->loginResponse = $this->post('user', 'login', array(
				'username' => $this->config->username,
				'password' => $this->config->password
			));
		}
		
		return $this->loginResponse;
	}
	
	/**
	 * Token
	 *
	 * This method is stupid: it assumes that login worked.
	 * If login is incorrect you'll learn about it on your next call.
	 *
	 * @return \Dope\Service\Drupal
	 */
	public function token()
	{
		if (! $this->tokenResponse) {
			$this->tokenResponse = $this->post('user', 'token');
		}
	
		return $this->tokenResponse;
	}
	
	/**
	 * 
	 * @param string $resource
	 * @param string $action
	 * @param array $params
	 * @return \Zend_Http_Response
	 */
	public function post($resource, $action = null, array $params=array())
	{
		return $this->call($resource, $action, $params, 'POST');
	}
	
	/**
	 *
	 * @param string $resource
	 * @param string $action
	 * @param array $params
	 * @return \Zend_Http_Response
	 */
	public function put($resource, $action = null, array $params=array())
	{
		return $this->call($resource, $action, $params, 'PUT');
	}
	
	/**
	 *
	 * @param string $resource
	 * @param string $action
	 * @param array $params
	 * @return \Zend_Http_Response
	 */
	public function get($resource, $action = null, array $params=array())
	{
		return $this->call($resource, $action, $params, 'GET');
	}
	
	/**
	 *
	 * @param string $resource
	 * @param string $action
	 * @param array $params
	 * @return \Zend_Http_Response
	 */
	public function delete($resource, $action = null, array $params=array())
	{
		return $this->call($resource, $action, $params, 'DELETE');
	}
	
	/**
	 * 
	 * @param string $resource
	 * @param string $action
	 * @param array $params
	 * @return mixed
	 */
	protected function call($resource, $action = null, array $params=array(), $method = 'GET')
	{
		$this->getClient()->setUri(
			$this->config->baseurl . $this->buildPath($resource, $action)
		);
		
		if ($this->loginResponse) {
			$this->getClient()->setHeaders('Cookie', join('=', array(
				$this->loginResponse->json->session_name,
				$this->loginResponse->json->sessid
			)));
		}
		
		if ($this->tokenResponse) {
			$this->getClient()->setHeaders('X-CSRF-Token',
				$this->tokenResponse->json->token
			);
		}
		
		$this->getClient()->setRawData(
			json_encode($params),
			'application/json'
		);
		
		$this->getClient()->request($method);
		
		$this->getClient()->getLastResponse()->json = json_decode(
			$this->getClient()->getLastResponse()->getBody()
		);
		
		if ($this->isDebug()) {
			print_r($this->getClient()->getLastRequest());
			print_r($this->getClient()->getLastResponse());
		}
		
		return $this->getClient()->getLastResponse();
	}
	
	/**
	 *
	 * @return \Zend_Http_Client
	 */
	public function getClient()
	{
		if (! $this->client instanceof \Zend_Http_Client) {
			$this->client = new \Zend_Http_Client();
		}
	
		return $this->client;
	}
	
	/**
	 * 
	 * @param string $resource
	 * @param string $action
	 * @return string
	 */
	protected function buildPath($resource, $action = null)
	{
		$path = $this->config->endpoint . '/' . $resource;
		
		if ($action) {
			$path .= '/' . $action;
		}
		
		$path .= '.json';
		
		return $path;
	}
}