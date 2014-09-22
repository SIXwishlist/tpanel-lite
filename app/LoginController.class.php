<?php

/**
 * Login Controller
 *
 * Handles user login and authentication
 */

namespace App;
use Base\MVC\Controller;
use Base\App;
use Base\Form;

class LoginController extends Controller
{
	// Initializes the login controller and redirects if the user is logged in
	function init ($request, $view)
	{
		if (App::Auth('Admin')->enabled())
		{
			App::redirect('@AdminController::home');
		}
		elseif (App::Auth('Client')->enabled())
		{
			$this->redirect();
		}
	}
	
	// Redirects the user based on an attempted URL
	protected function redirect ()
	{
		if (App::Session('Redirect')->exists('url'))
		{
			$url = App::Session('Redirect')->get('url');
			App::Session('Redirect')->delete('url');
			App::redirect($url);
		}
		else
		{
			App::redirect('@ClientController::home');
		}
	}
	
	// Renders the client login page
	function client ($request, $view)
	{
		$view->title = 'Login';
		if ($request->isPost())
		{
			$user = $request->post('username');
			$pass = $request->post('password');
			
			list($result, $userId) = $this->User->isClient($user, $pass);
			if ($result === true)
			{
				App::Auth('Client')->enable();
				App::Auth('Client')->set('username', $user);
				App::Auth('Client')->set('userId', $userId);
				App::flash('Login successful');
				$this->redirect();
			}
			else
			{
				$view->message = 'Invalid username or password';
			}
		}
		
		$form = new Form($request);
		
		$view->username = $form->text('username');
		$view->password = $form->password('password');
		$view->submit = $form->submit('funcbtn1', ['value' => 'Login']);
		$view->render('login');
	}
	
	// Renders the admin login page
	function admin ($request, $view)
	{
		$view->title = 'Login';
		if ($request->isPost())
		{
			$user = $request->post('username');
			$pass = $request->post('password');
			
			list($result, $userId) = $this->User->isAdmin($user, $pass);
			if ($result === true)
			{
				App::Auth('Admin')->enable();
				App::Auth('Admin')->set('username', $user);
				App::Auth('Admin')->set('userId', $userId);
				App::flash('Login successful');
				App::redirect('@AdminController::home');
			}
			else
			{
				$view->message = 'Invalid username or password';
			}
		}
		$form = new Form($request);
		
		$view->username = $form->text('username');
		$view->password = $form->password('password');
		$view->submit = $form->submit('funcbtn1', ['value' => 'Login']);
		$view->render('login_admin');
	}
}