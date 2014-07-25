<?php
// COMPLETE
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
	function __construct ()
	{
		$this->init();
	}
	
	function init ()
	{
	
	}
	
	function __get ($modelName)
	{
		return App::Model($modelName);
	}
}