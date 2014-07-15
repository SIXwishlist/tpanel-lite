<?php

/**
 * View
 *
 * Handles presentation of the AppFrame Lite application through direction by
 * a Controller and uses a ViewAdapter for processing View files.
 */

namespace Base\MVC;

class View
{
	protected static $theme = null;
	protected static $adapter = null;
	
	function setPath ($p)
	{
	
	}
	
	function __get ($key)
	{
	
	}
	
	function __set ($key, $value)
	{
	
	}
	
	function render ($tplFile)
	{
	
	}
	
	function renderAsJSON ()
	{
	
	}
	
	public static function setAdapter ($name)
	{
		$className = '\\Base\\Render\\'.$name;
		self::$adapter = new $className();
	}
	
	public static function getTheme ()
	{
		return self::$theme;
	}
	
	public static function setTheme ($theme)
	{
		self::$theme = $theme;
	}
}