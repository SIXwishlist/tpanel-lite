<?php
// COMPLETE
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
	protected $post;
	protected $get;
	protected $headers;
	protected $params;
	
	function __construct ($uri)
	{
		$this->uri = $uri;
		$this->get = $_GET;
		$this->post = $_POST;
		$this->headers = getallheaders();
		$this->params = array();
	}
	
	function isHTTPS ()
	{
	
	}
	
	function getDomainName ()
	{
	
	}
	
	function get ($key, $default = null)
	{
		return isset($this->get[$key]) ? $this->get[$key] : $default;
	}
	
	function post ($key, $default = null)
	{
		return isset($this->post[$key]) ? $this->post[$key] : $default;
	}
	
	function file ($id, $default = null)
	{
		return isset($_FILES[$id]) ? $this->generateFileArray($_FILES[$id]) : $default;
	}
	
	protected function generateFileArray ($file)
	{
		return ['filename' => $file['name'],
				'size' => $file['size'],
				'error' => isset($file['error']) ? $file['error'] : false,
				'tmp' => $file['tmp_name']];
	}
	
	function postArray ()
	{
		return $this->post;
	}
	
	function isPost ()
	{
		return strcmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
	}
	
	function URL ()
	{
		return $this->uri;
	}
	
	function param ($key, $default = null)
	{
		return isset($this->params[$key]) && $this->params[$key] != '' ? $this->params[$key] : $default;
	}
	
	function posted ($key)
	{
		return isset($this->post[$key]);
	}
	
	function header ($key)
	{
		return $this->headers[$key];
	}
	
	function params ($data)
	{
		$this->params = $data;
	}
}