<?php

/**
 * Exception
 *
 * Extension of the SPL Exception class with a title for use as a Web-based
 * error message.
 */

namespace Base;

class Exception extends \Exception
{
	function __construct ($title, $message)
	{
	
	}
}