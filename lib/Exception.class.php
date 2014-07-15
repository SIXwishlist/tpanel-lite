<?php
// COMPLETE
/**
 * Exception
 *
 * Extension of the SPL Exception class with a title for use as a Web-based
 * error message.
 */

namespace Base;

class Exception extends \Exception
{
	protected $title;
	protected $message;
	
	function __construct ($title, $message)
	{
		parent::__construct($title);
		$this->title = $title;
		$this->message = $message;
	}
	
	function getTitle ()
	{
		return $this->title;
	}
	
	function getMessage ()
	{
		return $this->message;
	}
}