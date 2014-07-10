<?php

namespace App;
use Base\App;

class AdminController extends AdminBase
{	
	function home ($request, $view)
	{
		$view->render('home');
	}
	
	function config ($request, $view)
	{
		$form = new Form();
		
		if ($request->isPost())
		{
			$view->success = $this->Config->store($request->postArray()) === true;
		}
		
		$form->assign($this->Config->toArray());
		$view->form = $form;
	}
	
	function logout ($request, $view)
	{
		App::Auth('Admin')->disable();
		App::flash('You have successfully logged out');
		App::redirect('@LoginController::admin');
	}
}