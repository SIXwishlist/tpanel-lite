<?php
// COMPLETE
/**
 * Auth
 *
 * Provides simplified access to an authentication layer for restricting user
 * access based on logins via a session.
 */

namespace Base;

class Auth
{
	protected $session;
	
	function __construct ($group)
	{
		$this->session = new Session('Auth_'.$group);
		$this->enable();
	}
	
	function enabled ()
	{
		return $this->session->get('__active', false) === true;
	}
	
	function get ($key)
	{
		return $this->session->set($key, null);
	}
	
	function set ($key, $value)
	{
		$this->session->set($key, $value);
	}
	
	function disable ()
	{
		$this->session->delete('__active');
	}
	
	function enable ()
	{
		$this->session->set('__active', true);
	}
}