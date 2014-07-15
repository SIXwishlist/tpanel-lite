<?php

/**
 * Controller
 *
 * Provides the necessary foundation for a functional controller used in an
 * AppFrame Lite application along with useful helper methods.
 */

namespace Base\MVC;

abstract class Controller
{
	protected $models = array();
	
	function __get ($modelName)
	{
		if (!isset($this->models[$modelName]))
		{
			$modelClass = '\\App\\Models\\'.$modelName;
			$this->models[$modelName] = new $modelClass();
		}
		return $this->models[$modelName];
	}
}