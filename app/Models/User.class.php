<?php

namespace App\Models;
use Base\Model\DbModel;

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
		return ($data->min(1) && $data->data('user_level') === 1);
	}
	
	function isAdmin ($user, $pass)
	{
		$data = $this->filter('username', $user)->filter('password', ['MD5(?)' => [$pass]]);
		return ($data->min(1) && $data->data('user_level') === 2);
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
		$v->assert($this->userExists($data['username']), sprintf('Username "%s" is already taken', $data['username']));
		$v->assert($this->emailExists($data['email']), 'Email address already in use for another account');
		return $v;
	}
	
	function create ($data)
	{
		// set userId
	}
	
	function sendActivationEmail ($email)
	{
	
	}
	
	function activate ($activationCode)
	{
	
	}
	
	function listUsers ($page = 0)
	{
	
	}
	
	function count ()
	{
	
	}
	
	function deleteUser ($userId)
	{
	
	}
	
	function modify ($userId, $data)
	{
	
	}
}