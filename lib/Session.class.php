<?php

/**
 * Session
 *
 * Allows interaction with PHP's session management functions for storing and
 * retrieving session-based information relevant to the user.
 */

namespace Base;

class Session
{
	protected $group;
	protected static $started = false;
	
	function __construct ($group)
	{
		if (!isset($_SESSION) && !self::$started)
		{
			self::$started = true;
			session_start();
		}
		$this->group = $group;
	}
	
	function get ($key, $default = null)
	{
		return isset($_SESSION[$this->group]) && isset($_SESSION[$this->group][$key]) ? $_SESSION[$this->group][$key] : $default;
	}
	
	function set ($key, $value)
	{
		if (!isset($_SESSION[$this->group]))
		{
			$_SESSION[$this->group] = array();
		}
		$_SESSION[$this->group][$key] = $value;
	}
	
	function delete ($key)
	{
		if ($this->exists($key))
		{
			unset($_SESSION[$this->group][$key]);
		}
	}
	
	function exists ($key)
	{
		return isset($_SESSION[$this->group]) && isset($_SESSION[$this->group][$key]);
	}
}