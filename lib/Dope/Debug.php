<?php

namespace Dope;

use Doctrine\DBAL\Logging\SQLLogger;

class Debug implements SQLLogger
{
	protected $punches;
	protected $data;
	protected $queries;
	
	public function __construct()
	{
		$this->punches = new ArrayObject();
		$this->data = new ArrayObject();
	}
	
	public function punch($class, $line, $comment=false)
	{
		$this->punches[] = new ArrayObject(array(
			'class' => $class,
			'line' => $line,
			'microtime' => microtime(true),
			'comment' => $comment
		));
		
		return $this;
	}
	
	public function log($name, $data)
	{
		$this->data[] = new ArrayObject(array(
			'name' => $name,
			'data' => $data
		));
		
		return $this;
	}
	
	public function getPunches()
	{
		return $this->punches;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	public function getQueries()
	{
		return $this->queries;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function startQuery($sql, array $params = null, array $types = null)
	{
		$this->queries[] = new ArrayObject(array(
			'error' => false,
			'sql' => $sql,
			'params' => $params,
			'types' => $types,
			'microtime' => new ArrayObject(array(
				'start' => microtime(true),
				'end' => null
			))
		));
		
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function stopQuery($error=false)
	{
		$lastQuery = $this->queries[count($this->queries) - 1];
		$lastQuery['microtime']['end'] = microtime(true);
		
		if ($error) {
			$lastQuery['error'] = $error;
		}
	}
}