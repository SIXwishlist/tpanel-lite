<?php

use Base\App;
use Base\MVC\View;

class Application
{
	public $theme = null;
	
	function __construct ()
	{
		$conf = parse_ini_file(App::Data('tpanel.conf')->getFullPath());
		
		$this->theme = $conf['theme'];
		View::setAdapter('Template');
	}
}