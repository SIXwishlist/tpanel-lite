<?php

namespace App;
use Base\MVC\Controller;
use Base\App;
use Base\Form;

class RegistrationController extends Controller
{
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
	
	function register ($request, $view)
	{
		$view->title = 'Register';
		$form = new Form($request);
		if ($request->isPost())
		{
			// Validate registration
			$data = $request->postArray();
			$v = $this->User->validateRegistration($data);
			if ($v->success() && $this->User->create($data))
			{
				$this->User->sendActivationEmail($data['email']);
				$view->success = true;
				$view->render('register_complete');
			}
			else
			{
				$view->success = false;
				$view->errors = $v->errors();
			}
		}
		$view->form = $form;
		$view->render('register');
	}
	
	function activate ($request, $view)
	{
		$view->title = 'Activate';
		$username = $request->param('user');
		$activationCode = $request->param('activation');
		
		$userId = $this->User->filter(['username' => $username])->data('user_id');
		
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