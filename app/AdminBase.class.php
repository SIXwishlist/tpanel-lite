<?php

/**
 * Admin Controller Base class
 *
 * Base class for an admin-only controller
 */

namespace App;
use Base\MVC\Controller;
use Base\App;

class AdminBase extends Controller
{
	// Current user ID
	protected $userId;
	// Return only JSON responses
	protected $jsonOnly = false;
	
	
	// Initializes the controller and checks permissions
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