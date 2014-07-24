<?php
// COMPLETE
namespace Base;

class Router
{
	protected $routes = [];
	protected $reversed = [];
	
	function load ($file)
	{
		$routes = parse_ini_file($file, true);
		foreach ($routes as $controller => $cRoutes)
		{
			foreach ($cRoutes as $method => $route)
			{
				$this->add($route, [$controller, $method]);
			}
		}
	}
	
	function reverseLookup ($controller, $params = null)
	{
		if (!isset($this->reversed[$controller]))
		{
			throw new Exception('URL Not Found', sprintf('"%s" could not be found', $controller));
		}
		
		$url = $this->reversed[$controller];
		
		if ($params !== null && is_array($params) && count($params) > 0)
		{
			foreach ($params as $key => $value)
			{
				$url = preg_replace('/\{(*|@)'.$key.'\}/', $value, $url);
			}
		}
		
		return Path::web($url);
	}
	
	function lookup ($route)
	{
		// Remove double slashes
		while (strpos($route, '//') !== false)
		{
			$route = str_replace('//', '/', $route);
		}
		
		// Remove beginning and ending slashes
		if (substr($route, 0, 1) === '/')
		{
			$route = substr($route, 1);
		}
		if (substr($route, -1) === '/')
		{
			$route = substr($route, 0, -1);
		}
		
		$params = array();
		
		$segments = array_reverse(explode('/', $route));
		$tmp = &$this->routes;
		while (count($segments) > 0)
		{
			$seg = array_pop($segments);
			
			// Match
			if (isset($tmp[$seg]))
			{
				$tmp = $tmp[$seg];
			}
			elseif (isset($tmp['@']))
			{
				$params[$tmp['@']['name']] = $seg;
				
				$tmp = $tmp['@']['next'];
			}
			elseif (isset($tmp['*']))
			{
				$params[$tmp['*']['name']] = implode('/', array_reverse($segments));
				$tmp = $tmp['*']['next'];
				$segments = null;
			}
			else
			{
				return false;
			}
		}
		
		// Not a callable
		if (!isset($tmp[0]) && !isset($tmp[1]))
		{
			return false;
		}
		
		$callable = $tmp;
		
		return [$callable, $params];
	}
	
	function add ($route, $callable)
	{
		// Remove beginning and ending slashes
		if (substr($route, 0, 1) === '/')
		{
			$route = substr($route, 1);
		}
		if (substr($route, -1) === '/')
		{
			$route = substr($route, 0, -1);
		}
		
		$this->reversed[$callable[0].'::'.$callable[1]] = $route;
		
		$segments = array_reverse(explode('/', $route));
		$tmp = &$this->routes;
		$seg = array_pop($segments);
		while (count($segments) > 0)
		{
			// Match special cases
			if (preg_match('/\{\*([A-Za-z0-9]+?)\}/', $seg, $match))
			{
				// Named wildcard
				if (!isset($tmp['*']))
				{
					$tmp['*'] = ['next' => array(), 'name' => $match[1]];
				}
				$tmp = &$tmp['*']['next'];
			}
			elseif (preg_match('/\{([A-Za-z0-9]+?)\}/', $seg, $match))
			{
				// Named parameter
				if (!isset($tmp['@']))
				{
					$tmp['@'] = ['next' => array(), 'name' => $match[1]];
				}
				$tmp = &$tmp['@']['next'];
			}
			else
			{
				if (!isset($tmp[$seg]))
				{
					$tmp[$seg] = array();
				}
				
				// Traverse
				$tmp = &$tmp[$seg];
			}
			
			$seg = array_pop($segments);
		}
		$tmp[$seg] = $callable;
	}
}