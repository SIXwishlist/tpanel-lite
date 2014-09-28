<?php

/**
 * Setup
 *
 * Provides functionality and control for installing and configuring the
 * application on a Web server.
 */

namespace Base;

class Setup
{
	// Current HTTP Request object
	protected $request = null;
	// Validator
	public $validate = null;
	// Content storage
	protected $content = [];
	
	// Event handler storage
	protected $before = null;
	protected $beforeSubmit = null;
	protected $submit = null;
	protected $afterSubmit = null;
	protected $validateEvent = null;
	protected $init = null;
	
	
	// Constructor
	function __construct ()
	{
		// Prepare Request
		$this->request = new Request('/');
	}
	
	/* Event handlers */
	
	// Event: before the setup is rendered
	function before ($callback)
	{
		$this->before = $callback;
	}
	
	// Event: after the setup is finished
	function afterSubmit ($callback)
	{
		$this->afterSubmit = $callback;
	}
	
	// Event: during data validation of a submission
	function validate ($callback)
	{
		$this->validateEvent = $callback;
	}
	
	// Event: before validation during a submission
	function beforeSubmit ($callback)
	{
		$this->beforeSubmit = $callback;
	}
	
	// Event: submission event
	function submit ($callback)
	{
		$this->submit = $callback;
	}
	
	// Event: setup initialization
	function init ($callback)
	{
		$this->init = $callback;
	}
	
	
	/* Main methods */
	
	function dbConnect ($id)
	{
	
	}
	
	function dbSettings ($id, $settings)
	{
	
	}
	
	function dbCreate ($id, $contentId)
	{
	
	}
	
	function beginGroup ($text)
	{
	
	}
	
	function endGroup ()
	{
	
	}
	
	function textField ($label, $id, $default = null)
	{
	
	}
	
	function passwordField ($label, $id, $default = null)
	{
	
	}
	
	// Returns the Request object generated from the built-in controller
	function getRequest ()
	{
		return $this->request;
	}
	
	function success ($msg)
	{
	
	}
	
	// Causes the Setup to cease execution and display an error
	function error ($msg)
	{
		throw new Exception('Setup Error', $msg);
	}
	
	// Returns the array of HTTP POST values from the request
	function post ()
	{
		return $this->request->postArray();
	}
	
	// Returns a value from the HTTP POST array
	function postValue ($id)
	{
		return $this->request->post($id);
	}
	
	// Sets a block of content for DB / file purposes
	function contentSet ($id, $contents)
	{
		$this->content[$id] = $contents;
	}
	
	// Creates a new file and writes a content block to it
	function fileCreate ($file, $contentId)
	{
		$fp = new File($file);
		if (!isset($this->content[$contentId]))
		{
			throw new Exception('Content Error', sprintf('The requested content block "%s" was never set', $contentId));
		}
		if (!$fp->contents($this->content[$contentId]))
		{
			throw new Exception('File Create Error', sprintf('Could not create file "%s"', $file));
		}
		
		return true;
	}
	
	// Creates a directory
	function dirCreate ($dir, $perms)
	{
		$d = new Dir($dir);
		if (!$d->create($perms))
		{
			throw new Exception('Directory Create Error', sprintf('Could not create directory "%s"', $dir));
		}
		
		return true;
	}
	
	// Returns true if a directory path is writable
	function dirIsWritable ($dir)
	{
		$d = new Dir($dir);
		return $d->isWritable();
	}
	
	// Removes calling PHP file
	function dissolve ()
	{
		$f = new File(__FILE__);
		return $f->delete();
	}
	
	function run ()
	{
		try
		{
			$this->executeSetup();
		}
		catch (Exception $e)
		{
		
		}
		
		// Render template
	}
	
	// Invokes a callback if it's not null
	protected function invoke ($callback, $params)
	{
		if ($callback !== null)
		{
			call_user_func_array($callback, $params);
		}
	}
	
	// Executes the body of code run by the setup program
	protected function executeSetup ()
	{
		// Init
		$this->invoke($this->init, [$this]);
		
		// Before
		$this->invoke($this->before, [$this]);
		
		if ($this->request->isPost())
		{
			$this->invoke($this->beforeSubmit, [$this]);
			
			// Validate & submit
			$this->invoke($this->validate, [$this, $this->post()]);
			$this->invoke($this->submit, [$this]);
			
			$this->invoke($this->afterSubmit, [$this]);
		}
	}
}