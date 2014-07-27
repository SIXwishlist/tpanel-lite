<?php

namespace App\Models;
use Base\MVC\Model;
use Base\Arr;
use Base\App;
use Base\Exception;

class Config extends Model
{
	protected $file;
	protected $keys;
	protected $conf;
	
	function init ()
	{
		$this->conf = null;
		$this->keys = ['web_host_name', 'db_server', 'db_user', 'db_pass',
		               'db_name', 'db_port', 'db_prefix', 'free_space',
					   'admin_email', 'theme', 'user_dir'];
		$this->file = App::Data('tpanel.conf')->getFullPath();
	}
	
	function get ($key)
	{
		if ($this->conf === null)
		{
			$this->conf = parse_ini_file($this->file);
		}
		
		if ($this->conf === false || !isset($this->conf[$key]))
		{
			throw new Exception('Configuration Error', 'The configuration file is corrupted');
		}
		return $this->conf[$key];
	}
	
	function getUserDir ()
	{
		return $this->get('user_dir');
	}
	
	function getFrontendURL ()
	{
		return Path::web('/');
	}
	
	function getAdminEmail ()
	{
		return $this->get('admin_email');
	}
	
	function getWebHostName ()
	{
		return $this->get('web_host_name');
	}
	
	function store ($data)
	{
		$data = Arr::filter($data, $this->keys);
		if (count($data) === 0)
		{
			// Nothing to write
			return false;
		}
		else
		{
			// Write to config file
			$f = fopen($this->file, 'w');
			foreach ($data as $key => $value)
			{
				fwrite($f, sprintf("%s=\"%s\"\n", $key, addslashes($value)));
			}
			fclose($f);
			return true;
		}
	}
	
	function toArray ()
	{
		return parse_ini_file($this->file);
	}
}