<?php

/**
 * App
 *
 * Handles application-specific tasks that are most frequently used or provide
 * a slight advantage for objects that would be created traditional 
 * instantiation.
 */

namespace Base;
use Base\MVC\View;
use Base\IO\File;

class App
{
	protected static $auth = array();
	protected static $app = null;
	
	public static function execute ($request)
	{
		// CODE GOES HERE
		
		self::invoke(self::$app, 'afterRun');
	}
	
	protected static function invoke ($obj, $method)
	{
		if (method_exists($obj, $method))
		{
			call_user_func(array($obj, $method));
		}
	}
	
	public static function init ()
	{
		include('app/config.php');
		
		// Check if "Application" class exists
		if (!class_exists('\\Application') && !class_exists('Application'))
		{
			throw new Exception('Config Exception', 'Configuration does not exist');
		}
		
		// Initialize App
		self::$app = new \Application();
		if (self::$app->theme !== null)
		{
			View::setTheme(self::$app->theme);
		}
		
		// Before running the app
		self::invoke(self::$app, 'beforeRun');
	}
	
	public static function Auth ($name)
	{
		// Assign Singletons
		if (!isset(self::$auth[$name]))
		{
			self::$auth[$name] = new Auth($name);
		}
		return self::$auth[$name];
	}
	
	public static function flash ($message)
	{
	
	}
	
	public static function redirect ($url)
	{
	
	}
	
	public static function Session ($name)
	{
	
	}
	
	public static function Data ($file)
	{
		return new File(Path::local('data/'.$file));
	}
}