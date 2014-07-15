<?php

/**
 * Request
 *
 * Encapsulates all request information such as $_GET, $_POST, and all headers
 * sent to the application from the browser.
 */

namespace Base;

class Request
{
	protected $uri;
	
	function __construct ($uri)
	{
		$this->uri = $uri;
	}
	
	function post ($key, $default = null)
	{
	
	}
	
	function postArray ()
	{
	
	}
	
	function isPost ()
	{
	
	}
	
	function URL ()
	{
		return $this->uri;
	}
	
	function param ($key, $default = null)
	{
	
	}
	
	function posted ($key)
	{
	
	}
	
	function header ($key)
	{
	
	}
	
	function params ($data)
	{
		$this->params = $data;
	}
}