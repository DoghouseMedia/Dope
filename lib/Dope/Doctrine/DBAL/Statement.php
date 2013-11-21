<?php

namespace Dope\Doctrine\DBAL;

class Statement extends \Doctrine\DBAL\Statement
{
	/**
	 * Executes the statement with the currently bound parameters.
	 *
	 * @param array $params
	 * @return boolean TRUE on success, FALSE on failure.
	 */
	public function execute($params = null)
	{
		$logger = $this->conn->getConfiguration()->getSQLLogger();
		if ($logger) {
			$logger->startQuery($this->sql, $this->params, $this->types);
		}
	
		try {
			$stmt = $this->stmt->execute($params);
		}
		catch (\Exception $e) {
			if (\Dope\Env::isDebug() AND $logger) {
				$logger->stopQuery($e->getMessage());
				return;
			}
			
			throw $e;
		}
		
		if ($logger) {
			$logger->stopQuery();
		}
		$this->params = array();
		$this->types = array();
		return $stmt;
	}
}