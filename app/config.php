<?php

/**
 * Application/Package configuration
 *
 * Initializes the application in the bootstrap
 */

use Base\App;
use Base\MVC\View;
use Base\Package;
use Base\Session;

class Application extends Package
{
	// No theme (yet)
	public $theme = null;
	
	// Constructor
	function __construct ()
	{
		$confFile = App::Data('tpanel.conf');
		if ($confFile->exists())
		{
			$conf = parse_ini_file($confFile->getFullPath());
		}
		else
		{
			$conf = [];
		}
		$this->theme = isset($conf['theme']) ? $conf['theme'] : null;
		View::setAdapter('PHTML');
		Session::setTimeout(8*60*60);
	}
}
