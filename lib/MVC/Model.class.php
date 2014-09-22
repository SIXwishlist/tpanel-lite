<?php

/**
 * Model
 *
 * Provides foundation for using a basic model in AppFrame Lite without any
 * prerequisites such as DB access.
 */

namespace Base\MVC;
use Base\App;

abstract class Model
{
	// Constructor
	function __construct ()
	{
		$this->init();
	}
	
	// Init overload (for inheritance purposes)
	function init ()
	{
	
	}
	
	// Returns a model loaded in the application
	function __get ($modelName)
	{
		return App::Model($modelName);
	}
}