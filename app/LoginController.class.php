<?php

namespace App;
use Base\MVC\Controller;
use Base\App;

class LoginController extends Controller
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
	
	function client ($request, $view)
	{
		if ($request->isPost())
		{
			$user = $request->post('username');
			$pass = $request->post('password');
			
			list($result, $userId) = $this->User->isClient($user, $pass);
			if ($result)
			{
				App::Auth('Client')->enable();
				App::Auth('Client')->set('username', $user);
				App::Auth('Client')->set('userId', $userId);
				App::flash('Login successful');
				App::redirect('@ClientController::home');
			}
			else
			{
				$view->message = 'Invalid username or password';
			}
		}
		$view->render('login');
	}
	
	function admin ($request, $view)
	{
		if ($request->isPost())
		{
			$user = $request->post('username');
			$pass = $request->post('password');
			
			list($result, $userId) = $this->User->isAdmin($user, $pass);
			if ($result)
			{
				App::Auth('Client')->enable();
				App::Auth('Client')->set('username', $user);
				App::Auth('Client')->set('userId', $userId);
				App::flash('Login successful');
				App::redirect('@ClientController::home');
			}
			else
			{
				$view->message = 'Invalid username or password';
			}
		}
		$view->render('login');
	}
}