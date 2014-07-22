<?php

/**
 * DbModel
 *
 * Extension of the Model class with DB access methods.
 */

namespace Base\MVC\Model;
use Base\MVC\Model;
use Base\Db\Filter;

class DbModel extends Model
{
	protected $db;
	protected $table;
	
	function __construct ()
	{
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
			$values .= '?';
			$params[] = $value;
		}
		$query = sprintf($q, $this->table, $keys, $values);
		$q = $this->db->sql($query);
		return $q->execute($params);
	}
	
	function delete ($key)
	{
	
	}
	
	function get ($id)
	{
		
	}
	
	function set ($id, $data)
	{
	
	}
	
	function display ($page, $rowCount)
	{
	
	}
	
	function rows ()
	{
	
	}
	
	function row_count ()
	{
	
	}
}