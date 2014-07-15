<?php
// COMPLETE
/**
 * Db
 *
 * PDO wrapper class for connecting to databases and querying information.
 */

namespace Base;
use \PDO;

class Db
{
	protected $pdo;
	protected $rowCount = 0;
	
	function __construct ($host, $user, $pass, $db, $type = 'mysql')
	{
		$connString = sprintf('%s:host=%s;dbname=%s', $type, $host, $db);
		$this->pdo = new PDO($connString, $user, $pass);
	}
	
	function rowsAffected ()
	{
		return $this->rowCount;
	}
	
	function sql ($sql)
	{
		$stmt = $this->pdo->prepare($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		return $stmt;
	}
	
	function query ($sql, $args = null)
	{
		if (is_array($args) && count($args) > 0)
		{
			$s = $this->sql($sql);
			$s->execute($args);
		}
		else
		{
			$s = $this->sql($sql);
			$s->execute();
		}
		return $s->fetchAll();
	}
	
	function execute ($sql, $args = null)
	{
		if (is_array($args) && count($args) > 0)
		{
			$s = $this->sql($sql);
			$result = $s->execute($args);
		}
		else
		{
			$s = $this->sql($sql);
			$result = $s->execute();
		}
		$this->rows = $s->rowCount();
		return $result;
	}
}