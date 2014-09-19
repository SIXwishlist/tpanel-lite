<?php

namespace App;
use Base\MVC\Controller;
use Base\App;

class AdminBase extends Controller
{
	protected $userId;
	protected $jsonOnly = false;
	
	function init ($request, $view)
	{
		if (!App::Auth('Admin')->enabled())
		{
			if ($this->jsonOnly === true)
			{
				$view->success = false;
				$view->error = 'Session has expired';
				$view->renderAsJSON();
				App::complete();
			}
			else
			{
				App::flash('You must be an administrator to use this system');
				App::redirect('@LoginController::admin');
			}
		}
		else
		{
			$view->setPath('admin');
			$this->userId = App::Auth('Admin')->get('userId');
		}
	}
}