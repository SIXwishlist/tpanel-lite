<?php

namespace App\Models;
use Base\Model\DbModel;

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
			if ($this->add($this->userId, ['backup_time' => ['NOW()'], 'backup_file_hash' => $hash]))
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
		// ZipArchive user's directory
	}
	
	function restore ()
	{
		// ZipArchive unzip backup to user's directory
	}
	
	function listFiles ()
	{
		// ZipArchive iterate with statIndex
	}
	
	function getDate ()
	{
		return $this->filter('user_id', $this->userId)->data('backup_time');
	}
}