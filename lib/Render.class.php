<?php

namespace Base;

abstract class Render
{
	protected $data;
	protected $layout;
	protected $path;
	protected $file;
	
	abstract function render ();
	
	protected function getRenderFile ()
	{
		// Use layout file or template depending on content
		if ($this->layout !== null)
		{
			$file = $this->layout;
		}
		else
		{
			$file = $this->file;
		}
		
		// Add path
		if ($this->path !== null)
		{
			$file = $this->path.'/'.$file;
		}
		return $file;
	}
	
	function setData ($data)
	{
		$this->data = $data;
	}
	
	function setLayout ($file)
	{
		$this->layout = $file;
	}
	
	function setContent ($file)
	{
		$this->file = $file;
	}
	
	function setPath ($path)
	{
		$this->path = $path;
	}
}
