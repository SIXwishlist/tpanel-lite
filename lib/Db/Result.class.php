<?php
// COMPLETE
/**
 * Result
 *
 * Contains results for a row inside of an object.
 */

namespace Base\Db;

class Result
{
	protected $data;
	
	function __construct ($data)
	{
		return $this->data = $data;
	}
	
	function __get ($key)
	{
		return $this->data[$key];
	}
	
	function toArray ()
	{
		return $this->data;
	}
}