<?php

/**
 * User Controller
 *
 * Manages user-related functions such as account management
 */

namespace App;
use Base\App;

class UserController extends AdminBase
{
	// Renders only JSON responses
	protected $jsonOnly = true;
	
	
	// Lists all users for a page
	function listUsers ($request, $view)
	{
		$page = (int)$request->param('page', 0);
		$view->users = $this->User->listUsers($page);
		$view->page = $page;
		$view->count = (int)$this->User->count();
		$view->perPage = (int)$this->User->perPage;
		$view->renderAsJSON();
	}
	
	// Removes a user account
	function removeUser ($request, $view)
	{
		$user = $request->param('user', false);
		
		if ((int)$user === (int)App::Auth('Admin')->get('userId'))
		{
			$view->user_success = false;
			$view->backup_success = false;
			$view->file_success = false;
			$view->message = 'You cannot delete your own account';
			$view->renderAsJSON();
			return;
		}
		
		$username = $this->User->get($user)->username;
		$this->FileSystem->setUser($username);
		$this->Backup->setUser($user);
		
		$view->backup_success = $this->Backup->destroy();
		$view->file_success = $this->FileSystem->destroy();
		$view->user_success = $this->User->deleteUser($user);
		$view->message = $this->User->message;
		$view->renderAsJSON();
	}
	
	// Creates a new user account
	function createUser ($request, $view)
	{
		$view->success = $this->User->createFromAdmin($request->postArray());
		$view->message = $this->User->message;
		
		$view->renderAsJSON();
	}
	
	// Modifies a user account
	function editUser ($request, $view)
	{
		$userId = $request->param('user', false);
		
		$view->success = $this->User->modify($userId, $request->postArray());
		$view->message = $this->User->message;
		
		$view->renderAsJSON();
	}
	
	// Returns the information for a user account
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