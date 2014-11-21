<?php

/**
 * User model
 *
 * Manages users in tPanel Lite
 */

namespace App\Models;
use DynamicTable\Model;
use Base\Mail;
use Base\Template;
use Base\Arr;
use Base\Validator;
use Base\App;
use Base\IO\Dir;

class User extends Model
{
	// Database connection info
	protected $db = 'main';
	protected $table = '[users]';
	
	// Current user ID
	protected $userId;
	// Error message
	public $message;
	// User list count (per page)
	public $perPage = 20;
	
	
	// Sets the current user's ID
	function setUser ($uid)
	{
		$this->userId = $uid;
	}
	
	// Returns the URL for a user's web space
	function getURL ()
	{
		return $this->Config->get('user_url').'/'.$this->usernameFromId($this->userId);
	}
	
	// Returns the username from an ID
	function usernameFromId ($uid)
	{
		return $this->query()->where('user_id', $uid)->limit(1)->result('username');
	}
	
	// Returns the user's path
	function getPath ()
	{
		// TODO: Consolidate with FileSystem method
		return $this->Config->getUserDir().'/'.$this->usernameFromId($this->userId).'/';
	}
	
	// Returns the user's available web space in bytes
	function availableSpace ()
	{
		// Convert to MB
		return $this->find($this->userId)->first('webspace') * 1024 * 1024;
	}
	
	// Validates an admin-side user account update
	function validateUpdate ($data)
	{
		$v = Validator::evaluate($data);
		return $v->required('email')->email('email')->required('full_name')->length('full_name', 3)->length('password', 6);
	}
	
	// Validates a client-side user account update
	function update ($data)
	{
		$data = Arr::filter($data, ['password', 'email', 'full_name']);
		$this->find($this->userId);
		return parent::update($data);
	}
	
	// Removes a user's account
	function remove ()
	{
		return $this->find($this->userId)->delete();
	}
	
	// Returns the configuration for a user's account
	function getConfig ()
	{
		$data = $this->find($this->userId)->first();
		return Arr::filter($data, ['username', 'email', 'full_name', 'webspace']);
	}
	
	// Returns true if a username and password match for a client
	function isClient ($user, $pass)
	{
		$data = $this->query()->where('username', $user)->andWhere('password', ['MD5(?)' => [$pass]])->limit(1);
		
		$result = ($data->count() > 0 && (int)$data->result('user_level') >= 1);
		if ($result)
		{
			$userId = $data->result('user_id');
		}
		else
		{
			$userId = null;
		}
		return [$result, $userId];
	}
	
	// Returns true if a username and password match for an admin
	function isAdmin ($user, $pass)
	{
		$data = $this->query()->where('username', $user)->andWhere('password', ['MD5(?)' => [$pass]])->limit(1);
		
		$result = ($data->count() > 0 && (int)$data->result('user_level') === 2);
		if ($result)
		{
			$userId = $data->result('user_id');
		}
		else
		{
			$userId = null;
		}
		return [$result, $userId];
	}
	
	// Validates a new user registration
	function validateRegistration ($data)
	{
		$v = Validator::evaluate($data);
		$v->required('username')->required('password_1', 'Password is required');
		$v->required('password_2', 'Password must be completed twice');
		$v->required('email')->required('full_name');
		$v->email('email');
		$v->length('username', 4, 48);
		$v->regex('username', '/^[A-Za-z0-9\_]+$/');
		$v->same('password_1', 'password_2', 'Passwords do not match');
		$v->length('password_1', 6, false, 'Password must be at least 6 characters long');
		$v->assert(!$this->userExists($data['username']), sprintf('Username "%s" is already taken', $data['username']));
		$v->assert(!$this->emailExists($data['email']), 'Email address already in use for another account');
		return $v;
	}
	
	// Validates an admin user account modification
	function validateModify ($id, $data)
	{
		$v = Validator::evaluate($data);
		$v->required('email')->required('full_name')->required('webspace');
		$v->email('email');
		$v->length('password', 6, false, 'Password must be at least 6 characters long');
		if (isset($data['email']))
		{
			$v->assert(!$this->emailInUse($id, $data['email']), 'Email address already in use for another account');
		}
		return $v;
	}
	
	// Returns true if a username exists
	protected function userExists ($username)
	{
		return $this->query()->where('username', $username)->limit(1)->count() > 0;
	}
	
	// Returns true if an email is already in use by another account
	protected function emailInUse ($id, $email)
	{
		// Check if the new email is in use by another account
		$uid = $this->query()->where('email', $email)->limit(1)->result('user_id');
		return $uid !== null && (int)$uid != (int)$id;
	}
	
	// Returns true if an email account exists for an account
	protected function emailExists ($email)
	{
		return $this->query()->where('email', $email)->limit(1)->count() > 0;
	}
	
	// Creates a new user account
	function create ($data)
	{
		// set userId
		$user = array();
		$user['username'] = $data['username'];
		$user['password'] = ['MD5(?)' => [$data['password_1']]];
		$user['email'] = $data['email'];
		$user['full_name'] = $data['full_name'];
		$user['webspace'] = $this->Config->getNewUserSpace();
		$user['user_level'] = 0;
		$user['activation_code'] = md5(date('Y-m-d g:i:s A'));
		$id = $this->insert($user);
		if ($id !== false)
		{
			$this->userId = $id;
			$d = new Dir($this->Config->getUserDir().'/'.$data['username']);
			if (!$d->create(0744))
			{
				return false;
			}
		}
		return $id;
	}
	
	// Creates a new user (admin-side)
	function createFromAdmin ($data)
	{
		$user = array();
		$user['username'] = $data['username'];
		$user['password'] = ['MD5(?)' => [$data['password']]];
		$user['email'] = $data['email'];
		$user['full_name'] = $data['full_name'];
		$user['webspace'] = $data['webspace'];
		$user['user_level'] = $data['user_level'];
		$id = $this->insert($user);
		if ($id !== false)
		{
			$d = new Dir($this->Config->getUserDir().'/'.$data['username']);
			if (!$d->create(0744))
			{
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// Sends a new user activation email
	function sendActivationEmail ($request, $email)
	{
		$r = $this->query()->where('user_id', $this->userId)->limit(1)->result();
		$tplFile = App::Data('emails/activate.tpl')->getFullPath();
		$template = Template::fromFile($tplFile);
		$template->email = $email;
		$template->username = $r['username'];
		$template->full_name = $r['full_name'];
		$template->activation_code = $r['activation_code'];
		$template->base_url = $this->Config->getFrontendURL($request);
		$template->user_url = $this->getURL();
		$template->user_id = $this->userId;
		
		$m = new Mail();
		$m->to($email);
		$m->from($this->Config->getAdminEmail());
		$m->subject(sprintf('%s - Account Activation Required', $this->Config->getWebHostEmail()));
		$m->body($template->_render());
		
		return $m->send();
	}
	
	// Activates a user's account
	function activate ($activationCode)
	{
		$r = $this->query()->where('activation_code', $activationCode)->andWhere('user_id', $this->userId)->limit(1);
		if ($r->count() > 0)
		{
			// Activated
			return $this->find($this->userId)->update(['user_level' => 1]);
		}
		else
		{
			return false;
		}
	}
	
	// Lists all users in the system
	function listUsers ($page = 0)
	{
		// NOTE: DB calls
		return $this->query()->limit($page*$this->perPage, $this->perPage)->orderBy('username', 'asc')->results();
	}
	
	// Returns a count of all users
	function count ()
	{
		// NOTE: DB call
		return $this->query()->count();
	}
	
	// Removes a user account from the database
	function deleteUser ($userId)
	{
		// NOTE: DB calls
		return $this->query()->where('user_id', $userId)->delete();
	}
	
	// Modifies a user's account in the database (admin-only)
	function modify ($userId, $data)
	{
		$data = Arr::filterNonNull($data, ['user_level', 'password', 'email', 'full_name', 'webspace']);
		
		// Validate form submissions
		$v = $this->validateModify($userId, $data);
		if (!$v->success())
		{
			$this->message = $v->error();
			return false;
		}
		
		// Encrypt the password
		if (isset($data['password']))
		{
			$data['password'] = ['MD5(?)' => [$data['password']]];
		}
		
		// NOTE: DB call
		if (!$this->find($userId)->update($data))
		{
			$this->message = 'One or more fields is missing';
			return false;
		}
		else
		{
			return true;
		}
	}
}
