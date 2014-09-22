<?php

/**
 * Client Controller Base class
 *
 * Clisnt-side controller for member access
 */

namespace App;
use Base\MVC\Controller;
use Base\App;
use Base\Path;

class ClientBase extends Controller
{
	// Username of the current user
	protected $username;
	// User ID of the current user
	protected $userId;

	/**
	 * Initialization for the Client Controller before the request is passed to
	 * a controller method.  If the user is not logged in, he/she is redirected
	 * to the login page.
	 *
	 * @param request the Request object of the current HTTP request
	 * @param view the HTTP response in the form of a View
	 */
	function init ($request, $view)
	{
		if (!App::Auth('Client')->enabled())
		{
			App::Session('Redirect')->set('url', Path::web($request->URL()));
			App::flash('You must be logged in to use this system');
			App::redirect('@LoginController::client');
		}
		else
		{
			$this->username = App::Auth('Client')->get('username');
			$this->userId = App::Auth('Client')->get('userId');
			
			$view->setPath('client');
		}
	}
}