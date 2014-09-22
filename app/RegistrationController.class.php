<?php

/**
 * Registration Controller
 *
 * Handles user registration and account activation
 */
 
namespace App;
use Base\MVC\Controller;
use Base\App;
use Base\Form;

class RegistrationController extends Controller
{
	// Initializes the controller and redirects the user if authenticated
	function init ($request, $view)
	{
		if (App::Auth('Admin')->enabled())
		{
			App::redirect('@AdminController::home');
		}
		elseif (App::Auth('Client')->enabled())
		{
			App::redirect('@ClientController::home');
		}
	}
	
	// Renders the registration page
	function register ($request, $view)
	{
		$view->title = 'Register';
		$form = new Form($request);
		if ($request->isPost())
		{
			// Validate registration
			$data = $request->postArray();
			$v = $this->User->validateRegistration($data);
			if (!$v->success())
			{
				$view->success = false;
				$view->errors = $v->errors();
			}
			else if (!$this->User->create($data))
			{
				$view->success = false;
				$view->errors = ['User directory could not be created -- please contact the web host administrator'];
			}
			else
			{
				$this->User->sendActivationEmail($request, $data['email']);
				$view->success = true;
				$view->render('register_complete');
			}
		}
		$view->form = $form;
		$view->render('register');
	}
	
	// Renders the account activation page
	function activate ($request, $view)
	{
		$view->title = 'Activate';
		$username = $request->param('user');
		$activationCode = $request->param('activation');
		
		$userId = $this->User->filter('username', $username)->data('user_id');
		$this->User->setUser($userId);
		
		if ($this->User->activate($activationCode))
		{
			$view->render('activate_successful');
		}
		else
		{
			$view->render('activate_unsuccessful');
		}
	}
}