<?php
// COMPLETE
/**
 * Controller
 *
 * Provides the necessary foundation for a functional controller used in an
 * AppFrame Lite application along with useful helper methods.
 */

namespace Base\MVC;
use Base\App;

abstract class Controller
{
	function __get ($modelName)
	{
		return App::Model($modelName);
	}
}