<?php

namespace App\Models;
use Base\MVC\Model\DbModel;
use Base\Mail;
use Base\Template;
use Base\Arr;
use Base\Validator;
use Base\App;

class User extends DbModel
{
	protected $db = 'main';
	protected $table = '[users]';
	protected $primaryKey = 'user_id';
	
	protected $userId;
	public $message;
	public $perPage = 20;
	
	function setUser ($uid)
	{
		$this->userId = $uid;
	}
	
	function usernameFromId ($uid)
	{
		return $this->filter('user_id', $uid)->data('username');
	}
	
	function getPath ()
	{
		return $this->Config->getUserDir().'/'.$this->User->usernameFromId($this->userId).'/';
	}
	
	function availableSpace ()
	{
		// Convert to MB
		return $this->get($this->userId)->webspace * 1024 * 1024;
	}
	
	function validateUpdate ($data)
	{
		$v = Validator::evaluate($data);
		return $v->required('email')->email('email')->required('full_name')->length('full_name', 3)->length('password', 6);
	}
	
	function update ($data)
	{
		$data = Arr::filter($data, ['password', 'email', 'full_name']);
		return $this->set($this->userId, $data);
	}
	
	function remove ()
	{
		return $this->delete($this->userId);
	}
	
	function getConfig ()
	{
		$data = $this->get($this->userId)->toArray();
		return Arr::filter($data, ['username', 'email', 'full_name', 'webspace']);
	}
	
	function isClient ($user, $pass)
	{
		$data = $this->filter('username', $user)->filter('password', ['MD5(?)' => [$pass]]);
		
		$result = ($data->min(1) && $data->data('user_level') >= 1);
		if ($result)
		{
			$userId = $data->data('user_id');
		}
		else
		{
			$userId = null;
		}
		return [$result, $userId];
	}
	
	function isAdmin ($user, $pass)
	{
		$data = $this->filter('username', $user)->filter('password', ['MD5(?)' => [$pass]]);
		
		$result = ($data->min(1) && (int)$data->data('user_level') === 2);
		if ($result)
		{
			$userId = $data->data('user_id');
		}
		else
		{
			$userId = null;
		}
		return [$result, $userId];
	}
	
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
	
	protected function userExists ($username)
	{
		return $this->filter('username', $username)->min(1);
	}
	
	protected function emailExists ($email)
	{
		return $this->filter('email', $email)->min(1);
	}
	
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
		$id = $this->add($user);
		if ($id !== false)
		{
			$this->userId = $id;
		}
		return $id;
	}
	
	function sendActivationEmail ($email)
	{
		$r = $this->filter('user_id', $this->userId);
		$tplFile = App::Data('emails/activate.tpl')->getFullPath();
		$template = Template::fromFile($tplFile);
		$template->email = $email;
		$template->username = $r->data('username');
		$template->full_name = $r->data('full_name');
		$template->activation_code = $r->data('activation_code');
		$template->base_url = $this->Config->getFrontendURL();
		$template->user_id = $this->userId;
		
		$m = new Mail();
		$m->to($email);
		$m->from($this->Config->getAdminEmail());
		$m->subject(sprintf('%s - Account Activation Required', $this->Config->getWebHostEmail()));
		$m->body($template->_render());
		
		return $m->send();
	}
	
	function activate ($activationCode)
	{
		$r = $this->filter('activation_code', $activationCode)->filter('user_id', $this->userId);
		if ($r->min(1))
		{
			// Activated
			return $this->set($this->userId, ['user_level' => 1]);
		}
		else
		{
			return false;
		}
	}
	
	function listUsers ($page = 0)
	{
		// NOTE: DB calls
		return $this->display($this->perPage, $page)->rows();
	}
	
	function count ()
	{
		// NOTE: DB call
		return $this->rowCount();
	}
	
	function deleteUser ($userId)
	{
		// NOTE: DB calls
		return $this->filter('user_id', $userId)->clear() > 0;
	}
	
	function modify ($userId, $data)
	{
		$data = Arr::filter($data, ['user_level', 'username', 'password', 'email', 'full_name', 'webspace']);
		// NOTE: DB call
		return $this->set($userId, $data);
	}
}
