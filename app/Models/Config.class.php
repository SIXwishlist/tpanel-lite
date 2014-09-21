<?php

namespace App\Models;
use Base\MVC\Model;
use Base\Arr;
use Base\App;
use Base\Exception;

class Config extends Model
{
	protected $file;
	protected $dbFile;
	protected $keys;
	protected $dbKeys;
	protected $conf;
	protected $dbConf;
	
	function init ()
	{
		$this->conf = null;
		$this->dbConf = null;
		$this->keys = ['web_host_name', 'free_space', 'admin_email', 
		               'theme', 'user_dir', 'user_url'];
		$this->dbKeys = ['server', 'username', 'password',
		                 'database', 'prefix'];
		$this->file = App::Data('tpanel.conf')->getFullPath();
		$this->dbFile = App::Data('databases/main.conf')->getFullPath();
	}
	
	function get ($key)
	{
		if (in_array($key, $this->dbKeys))
		{
			if ($this->dbConf === null)
			{
				$this->dbConf = parse_ini_file($this->dbFile);
			}
			
			if ($this->dbConf === false || !isset($this->dbConf[$key]))
			{
				throw new Exception('Database Configuration Error', 'The database configuration file is corrupted');
			}
			return $this->dbConf[$key];
		}
		elseif (in_array($key, $this->keys))
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
		else
		{
			throw new Exception('Configuration Key Error', 'A configuration key was requested that does not exist');
		}
	}
	
	function getUserDir ()
	{
		return $this->get('user_dir');
	}
	
	function getFrontendURL ()
	{
		return Path::web('/', true);
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
		$confData = Arr::filter($data, $this->keys);
		$dbData = Arr::filter($data, $this->dbKeys);
		if (count($confData) === 0 || count($dbData) === 0)
		{
			// Nothing to write
			return false;
		}
		else
		{
			// Write to config file
			$f = fopen($this->file, 'w');
			foreach ($confData as $key => $value)
			{
				fwrite($f, sprintf("%s=\"%s\"\n", $key, addslashes($value)));
			}
			fclose($f);
			
			// Write to database file
			$f = fopen($this->dbFile, 'w');
			foreach ($dbData as $key => $value)
			{
				fwrite($f, sprintf("%s=\"%s\"\n", $key, addslashes($value)));
			}
			fclose($f);
			return true;
		}
	}
	
	function getNewUserSpace ()
	{
		return $this->get('free_space');
	}
	
	function toArray ()
	{
		// Combine db and regular configuration files
		return parse_ini_file($this->dbFile) + parse_ini_file($this->file);
	}
}