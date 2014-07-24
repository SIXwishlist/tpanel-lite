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
	const NORMAL = 0;
	const LAZY = 1;
	
	protected $pdo;
	protected $rowCount = 0;
	protected $tablePrefix = null;
	
	public static function fromConfig ($name)
	{
		$info = parse_ini_file(App::Data('databases/'.$name.'.conf')->getFullPath());
		if (!isset($info['type']))
		{
			$type = 'mysql';
		}
		else
		{
			$type = $info['type'];
		}
		$db = new Db($info['server'], $info['username'], $info['password'], $info['database'], $type);
		if (isset($info['prefix']))
		{
			$db->setPrefix($info['prefix']);
		}
		return $db;
	}
	
	function __construct ($host, $user, $pass, $db, $type = 'mysql')
	{
		$connString = sprintf('%s:host=%s;dbname=%s', $type, $host, $db);
		$this->pdo = new PDO($connString, $user, $pass);
	}
	
	function setPrefix ($p)
	{
		$this->tablePrefix = $p;
	}
	
	function rowsAffected ()
	{
		return $this->rowCount;
	}
	
	function sql ($sql, $fetchMode = self::NORMAL)
	{
		if ($this->tablePrefix !== null)
		{
			$sql = preg_replace('/\[(.+?)\]/', $this->tablePrefix.'$1', $sql);
		}
		
		$stmt = $this->pdo->prepare($sql);
		switch ($fetchMode)
		{
			case self::LAZY:
				$stmt->setFetchMode(PDO::FETCH_LAZY);
				break;
			case self::NORMAL:
			default:
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
		}
		
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