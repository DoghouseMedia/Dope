<?php

namespace Dope\Doctrine\DBAL;

class Connection extends \Doctrine\DBAL\Connection
{
	/**
	 * Prepares an SQL statement.
	 *
	 * @param string $statement The SQL statement to prepare.
	 * @return \Dope\Doctrine\DBAL\Driver\Statement The prepared statement.
	 */
	public function prepare($statement)
	{
		$this->connect();
	
		$stmt = new Statement($statement, $this);
		$stmt->setFetchMode(\PDO::FETCH_ASSOC);
	
		return $stmt;
	}
}