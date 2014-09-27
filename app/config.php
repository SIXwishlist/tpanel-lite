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
		$conf = parse_ini_file(App::Data('tpanel.conf')->getFullPath());
		
		$this->theme = $conf['theme'];
		View::setAdapter('Template');
		Session::setTimeout(8*60*60);
	}
}