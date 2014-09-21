<?php

namespace App;

class UserController extends AdminBase
{
	protected $jsonOnly = true;
	
	function listUsers ($request, $view)
	{
		$page = (int)$request->param('page', 0);
		$view->users = $this->User->listUsers($page);
		$view->page = $page;
		$view->count = (int)$this->User->count();
		$view->perPage = (int)$this->User->perPage;
		$view->renderAsJSON();
	}
	
	function removeUser ($request, $view)
	{
		$user = $request->param('user', false);
		$username = $this->User->get($user)->username;
		$this->FileSystem->setUser($username);
		$this->Backup->setUser($user);
		
		$view->backup_success = $this->Backup->destroy();
		$view->file_success = $this->FileSystem->destroy();
		$view->user_success = $this->User->deleteUser($user);
		$view->message = $this->User->message;
		$view->renderAsJSON();
	}
	
	function createUser ($request, $view)
	{
		$view->success = $this->User->createFromAdmin($request->postArray());
		$view->message = $this->User->message;
		
		$view->renderAsJSON();
	}
	
	function editUser ($request, $view)
	{
		$userId = $request->param('user', false);
		
		$view->success = $this->User->modify($userId, $request->postArray());
		$view->message = $this->User->message;
		
		$view->renderAsJSON();
	}
	
	function getUser ($request, $view)
	{
		$userId = $request->param('user', false);
		
		// NOTE: Syntactic sugar -- fallback($this->User->get($userId))->toArray() => false if "get" returns false?
		$user = $this->User->get($userId);
		$view->userId = $userId;
		if (!$user)
		{
			$view->user = false;
		}
		else
		{
			$view->user = $user->toArray();
		}
		$view->message = $this->User->message;
		
		$view->renderAsJSON();
	}
}