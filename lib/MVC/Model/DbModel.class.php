<?php

/**
 * DbModel
 *
 * Extension of the Model class with DB access methods.
 */

namespace Base\MVC\Model;
use Base\App;
use Base\MVC\Model;
use Base\Db\Filter;
use Base\Db\Result;

class DbModel extends Model
{
	protected $db;
	protected $table;
	protected $primaryKey;
	
	function __construct ()
	{
		parent::__construct();
		$this->db = App::Database($this->db);
	}
	
	function filter ($key, $value)
	{
		// Return DB Filter
		$f = new Filter($this->db, $this->table);
		return $f->filter($key, $value);
	}
	
	function add ($data)
	{
		$q = 'INSERT INTO `%s` (%s) VALUES (%s)';
		list($keys, $values, $params) = $this->parseInsertRow($data);
		$query = sprintf($q, $this->table, $keys, $values);
		$q = $this->db->sql($query);
		return $q->execute($params);
	}
	
	protected function parseInsertRow ($data)
	{
		$keys = '';
		$values = '';
		$params = [];
		foreach ($data as $key => $value)
		{
			if (strlen($values) > 0)
			{
				$keys .= ',';
				$values .= ',';
			}
			$keys .= sprintf('`%s`', $key);
			if (is_array($value))
			{
				if (isset($value[0]))
				{
					// SQL Expression
					$values .= current($value);
				}
				else
				{
					// Expression with prepared segment
					$values .= key($value);
					$params = array_merge($params, current($value));
				}
			}
			else
			{
				$values .= '?';
				$params[] = $value;
			}
		}
		return [$keys, $values, $params];
	}
	
	function delete ($key)
	{
		$q = $this->db->sql(sprintf('DELETE FROM `%s` WHERE `%s` = ?', $this->table, $this->primaryKey));
		return $q->execute([$key]);
	}
	
	function get ($id)
	{
		$q = $this->db->sql(sprintf('SELECT * FROM `%s` WHERE `%s` = ? LIMIT 1', $this->table, $this->primaryKey));
		if ($q->execute([$id]))
		{
			return new Result($q->fetch(\PDO::FETCH_ASSOC));
		}
		else
		{
			return false;
		}
	}
	
	function set ($id, $data)
	{
		$updateValues = [];
		$updates = '';
		foreach ($data as $key => $value)
		{
			if ($updates !== '')
			{
				$updates .= ',';
			}
			
			if (is_array($value))
			{
				if (isset($value[0]))
				{
					$updates .= current($value);
				}
				else
				{
					$updates .= key($value);
					$updateValues[] = current($value);
				}
			}
			else
			{
				$updates .= sprintf('`%s` = ?', $key);
				$updateValues[] = $value;
			}
		}
		$q = $this->db->sql(sprintf('UPDATE `%s` SET %s WHERE `%s` = ?', $this->table, $updates, $this->primaryKey));
		return $q->execute(array_merge($updateValues, [$id]));
	}
	
	function display ($rowCount, $page)
	{
		$this->display = [$page * $rowCount, $rowCount];
		return $this;
	}
	
	function rows ()
	{
		if ($this->display !== null)
		{
			$q = $this->db->sql(sprintf('SELECT * FROM `%s` LIMIT %d, %d', $this->table, $this->display[0], $this->display[1]));
		}
		else
		{
			$q = $this->db->sql(sprintf('SELECT * FROM `%s`', $this->table));
		}
		if ($q->execute())
		{
			return $q->fetchAll();
		}
		else
		{
			return false;
		}
	}
	
	function rowCount ()
	{
		$q = $this->db->sql(sprintf('SELECT COUNT(*) as row_count FROM `%s`', $this->table));
		if ($q->execute())
		{
			return (int)$q->fetch(\PDO::FETCH_ASSOC)['row_count'];
		}
		else
		{
			return false;
		}
	}
}