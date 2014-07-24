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
	
	function __construct ($title, $message)
	{
		parent::__construct($message);
		$this->title = $title;
	}
	
	function getTitle ()
	{
		return $this->title;
	}
}