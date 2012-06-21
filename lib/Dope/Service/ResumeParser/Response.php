<?php

namespace Dope\Service\ResumeParser;

abstract class Response
{
	/**
	 * Response object
	 * 
	 * @var stdClass
	 */
	protected $object;
	
	abstract public function getHrXml();
	abstract public function getResumeContents();
	
	public function __construct(\stdClass $object)
	{
		$this->object = $object;
	}
	
	public function __get($key)
	{
		return $this->object->$key;
	}
	
	abstract public function isSuccess();
}