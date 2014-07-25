<?php
// COMPLETE
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
	protected static $router = null;
	protected static $dbs = array();
	protected static $models = array();
	
	public static function route (&$request)
	{
		$uri = $request->URL();
		$result = self::$router->lookup($uri);
		if ($result !== false)
		{
			$request->params($result[1]);
			return $result[0];
		}
		else
		{
			return false;
		}
	}
	
	public static function displayNotFound ($request)
	{
		print "<h1>Not Found</h1>";
		print $request->URL().' was not found';
		self::complete();
	}
	
	public static function execute ($request)
	{
		try
		{
			$callable = self::route($request);
			if ($callable === false)
			{
				self::displayNotFound($request);
			}
			
			self::run($request, $callable);
			
			self::invoke('afterRun');
		}
		catch (Exception $e)
		{
			self::displayException($e, false);
		}
	}
	
	public static function displayException ($e, $continue = true)
	{
		print "<fieldset><legend>".$e->getTitle()."</legend><p>".$e->getMessage()."</p></fieldset>";
		if ($continue !== true)
		{
			self::complete();
		}
	}
	
	public static function complete ()
	{
		die;
	}
	
	protected static function run ($request, $callable)
	{
		$view = new View();
		
		$controller = '\\App\\'.$callable[0];
		$c = new $controller();
		call_user_func([$c, 'init'], $request, $view);
		call_user_func([$c, $callable[1]], $request, $view);
	}
	
	protected static function invoke ($method)
	{
		if (method_exists(self::$app, $method))
		{
			call_user_func(array(self::$app, $method));
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
		self::$router = self::$app->getRouter();
		self::$router->load('app/routes.conf');
		
		// Before running the app
		self::invoke('beforeRun');
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
	
	public static function Database ($name)
	{
		if (!isset(self::$dbs[$name]))
		{
			self::$dbs[$name] = Db::fromConfig($name);
		}
		return self::$dbs[$name];
	}
	
	public static function Model ($name)
	{
		if (!isset(self::$models[$name]))
		{
			$model = '\\App\\Models\\'.$name;
			self::$models[$name] = new $model();
		}
		return self::$models[$name];
	}
	
	public static function flash ($message)
	{
		View::flashMessage($message);
	}
	
	public static function redirect ($url, $params = null)
	{
		if (substr($url, 0, 1) === '@')
		{
			$url = self::$router->reverseLookup(substr($url, 1), $params);
		}
		header('Location: '.$url);
		self::complete();
	}
	
	public static function Session ($name)
	{
		return new Session($name);
	}
	
	public static function Data ($file)
	{
		return new File(Path::local('data/'.$file));
	}
}