<?php
// COMPLETE
/**
 * Path
 *
 * Contains application path-based and URI-based methods for resolving paths
 * dependent on the application's physical location either on the server or
 * in the browser.
 */

namespace Base;
use Base\MVC\View;

class Path
{
	protected static $dir;
	protected static $uri;
	
	public static function initDir ($path)
	{
		if (substr($path, -1) !== '/')
		{
			$path .= '/';
		}
		self::$dir = $path;
	}
	
	public static function initURI ()
	{
		if (!isset($_SERVER['PATH_INFO']))
		{
			$uri = '/';
		}
		else
		{
			$uri = str_replace($_SERVER['PATH_INFO'], '', $_SERVER['REQUEST_URI']);
		}
		
		if (substr($uri, -1) !== '/')
		{
			$uri .= '/';
		}
		self::$uri = $uri;
	}
	
	public static function web ($path)
	{
		if (substr($path, 0, 1) === '/')
		{
			$path = substr($path, 1);
		}
		return self::$uri.$path;
	}
	
	public static function local ($path)
	{
		if (substr($path, 0, 1) === '/')
		{
			$path = substr($path, 1);
		}
		return self::$dir.$path;
	}
	
	public static function theme ($path)
	{
		if (substr($path, 0, 1) === '/')
		{
			$path = substr($path, 1);
		}
		$themeDir = View::getTheme();
		if ($themeDir !== null)
		{
			$themeDir .= '/';
		}
		return self::web('themes/'.$themeDir.$path);
	}
}