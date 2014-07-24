<?php
// COMPLETE
/**
 * Filter
 *
 * Provides an abstracted class for querying a database.
 */
 
namespace Base\Db;
use Base\Db;

class Filter
{
	protected $db;
	protected $table;
	protected $queryData;
	
	function __construct ($db, $table)
	{
		$this->queryData = null;
		$this->db = $db;
		$this->table = $table;
	}
	
	protected function getSQL ($firstRow = false, $count = false)
	{
		$sql = 'SELECT ';
		if ($count === false)
		{
			$sql .= '*';
		}
		else
		{
			$sql .= 'COUNT(*) AS row_count';
		}
		$sql .= ' FROM `'.$this->table.'`';
		if (count($this->filters) > 0)
		{
			$sql .= ' WHERE ';
			$first = true;
			foreach ($this->filters as $combo)
			{
				if (!$first)
				{
					$sql .= ' AND ';
				}
				$first = false;
				if (is_array($combo[1]))
				{
					if (isset($combo[1][0]))
					{
						$sql .= sprintf('`%s` = %s', $combo[0], $combo[1][0]);
					}
					else
					{
						$sql .= sprintf('`%s` = %s', $combo[0], key($combo[1]));
					}
				}
				else
				{
					$sql .= sprintf('`%s` = ?', $combo[0]);
				}
			}
		}
		if ($firstRow === true)
		{
			$sql .= ' LIMIT 1';
		}
		return $sql;
	}
	
	protected function getDeleteSQL ()
	{
		$sql = 'DELETE FROM `'.$this->table.'`';
		if (count($this->filters) > 0)
		{
			$sql .= ' WHERE ';
			$first = true;
			foreach ($this->filters as $combo)
			{
				if (!$first)
				{
					$sql = ' AND ';
				}
				$first = false;
				if (is_array($combo[1]))
				{
					if (isset($combo[1][0]))
					{
						$sql .= sprintf('`%s` = %s', $combo[0], $combo[1][0]);
					}
					else
					{
						$sql .= sprintf('`%s` = %s', $combo[0], key($combo[1]));
					}
				}
				else
				{
					$sql .= sprintf('`%s` = ?', $combo[0]);
				}
			}
		}
		return $sql;
	}
	
	protected function getParams ()
	{
		if (count($this->filters) > 0)
		{
			$result = array();
			foreach ($this->filters as $combo)
			{
				$value = $combo[1];
				if (is_array($value))
				{
					if (isset($value[0]))
					{
						$result[] = $value[0];
					}
					else
					{
						// Expression with prepared segment
						$result = array_merge($result, current($value));
					}
				}
				else
				{
					$result[] = $value;
				}
			}
			return $result;
		}
		else
		{
			return null;
		}
	}
	
	protected function checkFirstRow ()
	{
		if ($this->queryData === null)
		{
			// Lazy loading to prevent passing too much data over the Db connection
			$q = $this->db->sql($this->getSQL(true), Db::LAZY);
			$q->execute($this->getParams());
			
			$this->queryData = $q->fetch(\PDO::FETCH_LAZY);
		}
	}
	
	function rows ()
	{
		$q = $this->db->sql($this->getSQL(false));
		$q->execute($this->getParams());
		
		return $q->fetchAll();
	}
	
	function data ($key)
	{
		$this->checkFirstRow();
		return $this->queryData->$key;
	}
	
	protected function getCount ()
	{
		$countSQL = $this->getSQL(false, true);
		$q = $this->db->sql($countSQL);
		$params = $this->getParams();
		
		if (is_array($params) && count($params) > 0)
		{
			$q->execute($params);
		}
		else
		{
			$q->execute();
		}
		
		$data = $q->fetch(\PDO::FETCH_ASSOC);
		if (isset($data['row_count']))
		{
			return (int)$data['row_count'];
		}
		else
		{
			return false;
		}
	}
	
	function min ($count)
	{
		return $this->getCount() >= $count;
	}
	
	function clear ()
	{
		// Delete where...
		$q = $this->db->sql($this->getDeleteSQL());
		return $q->execute($this->getParams());
	}
	
	function filter ($key, $value)
	{
		$this->filters[] = [$key, $value];
		
		return $this;
	}
}