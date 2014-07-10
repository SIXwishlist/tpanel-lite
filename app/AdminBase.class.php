<?php

namespace App;
use Base\MVC\Controller;
use Base\App;

class AdminBase extends Controller
{
	protected $userId;
	
	function init ($request, $view)
	{
		if (!App::Auth('Admin')->enabled())
		{
			App::flash('You must be an administrator to use this system');
			App::redirect('@LoginController::admin');
		}
		else
		{
			$view->setPath('admin');
			$this->userId = App::Auth('Admin')->get('userId');
		}
	}
}