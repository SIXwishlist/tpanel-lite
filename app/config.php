<?php

use Base\App;
use Base\MVC\View;
use Base\Package;

class Application extends Package
{
	public $theme = null;
	
	function __construct ()
	{
		$conf = parse_ini_file(App::Data('tpanel.conf')->getFullPath());
		
		$this->theme = $conf['theme'];
		View::setAdapter('Template');
	}
}