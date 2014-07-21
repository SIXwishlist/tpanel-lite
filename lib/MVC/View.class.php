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
	protected $data = array();
	protected $path = null;
	
	function setPath ($p)
	{
		$this->path = $p;
	}
	
	function __get ($key)
	{
		return $this->data[$key];
	}
	
	function __set ($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	function render ($tplFile)
	{
		// Check adapter
		if (self::$adapter === null)
		{
			self::setAdapter('PHTML');
		}
		
		if (self::$theme !== null)
		{
			$themePath = 'themes/'.self::$theme.'/';
			if ($this->path !== null)
			{
				self::$adapter->setPath($themePath.$this->path);
			}
			else
			{
				self::$adapter->setPath($themePath);
			}
			self::$adapter->setLayout('template');
		}
		elseif ($this->path !== null)
		{
			self::$adapter->setPath($this->path);
		}
		self::$adapter->setData($this->data);
		self::$adapter->setContent($tplFile);
		self::$adapter->render();
	}
	
	function renderAsJSON ()
	{
		header('Content-Type: application/json');
		print json_encode($this->data, true);
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
	
	function hasFlash ()
	{
		return App::Session('Flash')->exists('message');
	}
	
	function getFlash ()
	{
		return App::Session('Flash')->get('message');
	}
	
	public static function flashMessage ($msg)
	{
		App::Session('Flash')->set('message', $msg);
	}
}