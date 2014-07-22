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
	function filter ($key, $value)
	{
		// Return DB Filter
		return new Filter(App::Database($this->db), $this->table);
	}
	
	function add ($data)
	{
	
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