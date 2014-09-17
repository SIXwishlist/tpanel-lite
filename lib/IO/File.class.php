<?php
// COMPLETE
/**
 * File
 *
 * Provides file access including file system operations and retrieving
 * information about specific files.
 */

namespace Base\IO;
use Base\Filter;
use \SplFileInfo;

class File
{
	protected $file;
	
	function __construct ($file)
	{
		$this->file = $file;
	}
	
	function create ($perms = 0644)
	{
		touch($this->file);
		return @chmod($this->file, $perms);
	}
	
	function delete ()
	{
		return @unlink($this->file);
	}
	
	function copy ($dest)
	{
		return @copy($this->file, $dest);
	}
	
	function extension ()
	{
		return (new SplFileInfo($this->file))->getExtension();
	}
	
	function exists ()
	{
		return file_exists($this->file);
	}
	
	function basename ()
	{
		return basename($this->file);
	}
	
	function mime ()
	{
		$f = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($f, $this->file);
		finfo_close($f);
		return $mime;
	}
	
	function sizeHuman ()
	{
		return Filter::fileSize($this->size());
	}
	
	function size ()
	{
		return filesize($this->file);
	}
	
	function move ($dest)
	{
		return $this->copy($dest) && $this->delete();
	}
	
	function rename ($name)
	{
		$name = basename($name);
		$dir = dirname($this->file);
		return @rename($this->file, $dir.'/'.$name);
	}
	
	function contents ($newContents = null)
	{
		if ($newContents !== null)
		{
			if (!is_file($this->file) || is_writable($this->file))
			{
				$f = fopen($this->file, 'w');
				fwrite($f, $newContents);
				fclose($f);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return file_get_contents($this->file);
		}
	}
	
	function upload ($fileInfo = null)
	{
		if ($fileInfo !== null)
		{
			return $this->uploadFromRequest($fileInfo);
		}
		else
		{
			return $this->uploadFromInput();
		}
	}
	
	function uploadFromRequest ($fileInfo)
	{
		return isset($fileInfo['tmp']) && @move_uploaded_file($fileInfo['tmp'], $this->file);
	}
	
	function uploadFromInput ()
	{
		return $this->contents(file_get_contents('php://input'));
	}
	
	function getFullPath ()
	{
		return $this->file;
	}
	
	public static function isFile ($f)
	{
		return file_exists($f) && is_file($f);
	}
	
	public static function getExtension ($filename)
	{
		return (new File($filename))->extension();
	}
}