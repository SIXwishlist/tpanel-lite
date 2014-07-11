<?php

class Application
{
	function before_run ()
	{
		$conf = parse_ini_file(App::Data('tpanel.conf')->getFullPath());
		
		View::setTheme($conf['theme']);
		View::setAdapter('Template');
	}
}