<?php

namespace App\Models;
use Base\MVC\Model\DbModel;
use Base\App;
use Base\IO\File;
use Base\IO\Zip;

class Backup extends DbModel
{
	protected $db = 'main';
	protected $table = '[backups]';
	protected $primaryKey = 'user_id';
	
	protected $fs;
	protected $userId;
	
	function setFileSystem ($fs)
	{
		$this->fs = $fs;
	}
	
	function setUser ($userId)
	{
		$this->userId = $userId;
	}
	
	function getBackupFile ()
	{
		$r = $this->filter('user_id', $this->userId);
		
		// If the hash exists, return the filename, otherwise create it
		if ($r->min(1))
		{
			return sprintf('backups/%s', $r->data('backup_file_hash'));
		}
		else
		{
			$hash = md5(date('m/d/Y g:i:s A'));
			if ($this->add(['user_id' => $this->userId, 'backup_time' => ['NOW()'], 'backup_file_hash' => $hash]))
			{
				return sprintf('backups/%s', $hash);
			}
			else
			{
				return false;
			}
		}
	}
	
	function destroy ()
	{
		$this->filter('user_id', $this->userId)->clear();
		
		$backup = App::Data($this->getBackupFile())->getFullPath();
		if (File::isFile($backup))
		{
			$f = new File($backup);
			return $f->delete();
		}
		else
		{
			return true;
		}
	}
	
	function backup ()
	{
		$this->User->setUser($this->userId);
		$zip = new Zip(App::Data($this->getBackupFile())->getFullPath(), 'w');
		// true = recursive
		$zip->addDirectory($this->User->getPath(), true);
		return $zip->close();
	}
	
	function restore ()
	{
		$this->User->setUser($this->userId);
		$zip = new Zip(App::Data($this->getBackupFile())->getFullPath(), 'r');
		// true = overwrite duplicates
		$result = $zip->extractAll($this->User->getPath());
		$zip->close();
		return $result;
	}
	
	function listFiles ()
	{
		$file = App::Data($this->getBackupFile())->getFullPath();
		if (File::isFile($file))
		{
			$zip = new Zip($file, 'r');
			$files = $zip->listAll(true);
			$zip->close();
			return $files;
		}
		else
		{
			return false;
		}
	}
	
	function getDate ()
	{
		return $this->filter('user_id', $this->userId)->data('backup_time');
	}
}