<?php
// COMPLETE
/**
 * Model
 *
 * Provides foundation for using a basic model in AppFrame Lite without any
 * prerequisites such as DB access.
 */

namespace Base\MVC;

abstract class Model
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