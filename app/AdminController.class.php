<?php

/**
 * Admin Controller
 *
 * Administrative control panel controller class
 */

namespace App;
use Base\App;
use Base\Form;

class AdminController extends AdminBase
{
	// Renders the main user list page
	function home ($request, $view)
	{
		$view->title = 'User List';
		$view->render('home');
	}
	
	// Renders the configuration page
	function config ($request, $view)
	{
		$view->title = 'Configuration';
		
		$form = new Form($request);
		
		if ($request->isPost())
		{
			$view->success = $this->Config->store($request->postArray()) === true;
		}
		
		$form->assign($this->Config->toArray());
		$view->form = $form;
		$view->render('config');
	}
	
	// Admin logout
	function logout ($request, $view)
	{
		App::Auth('Admin')->disable();
		App::flash('You have successfully logged out');
		App::redirect('@LoginController::admin');
	}
}