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
	// Session group
	protected $group;
	// Session start flag
	protected static $started = false;
	
	
	// Constructor (requires a group)
	function __construct ($group)
	{
		if (!isset($_SESSION) && !self::$started)
		{
			self::$started = true;
			session_start();
		}
		$this->group = $group;
	}
	
	// Returns a value from the session manager ($default is returned if the key isn't found)
	function get ($key, $default = null)
	{
		return isset($_SESSION[$this->group]) && isset($_SESSION[$this->group][$key]) ? $_SESSION[$this->group][$key] : $default;
	}
	
	// Sets a key-value pair in the session manager
	function set ($key, $value)
	{
		if (!isset($_SESSION[$this->group]))
		{
			$_SESSION[$this->group] = array();
		}
		$_SESSION[$this->group][$key] = $value;
	}
	
	// Removes a value from the session manager
	function delete ($key)
	{
		if ($this->exists($key))
		{
			unset($_SESSION[$this->group][$key]);
		}
	}
	
	// Returns true if the key for a value exists in the session manager
	function exists ($key)
	{
		return isset($_SESSION[$this->group]) && isset($_SESSION[$this->group][$key]);
	}
}