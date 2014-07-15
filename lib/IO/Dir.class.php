<?php
// COMPLETE
/**
 * Dir
 *
 * Encapsulates directory access, iteration, and file system operations that
 * involve directory creation, modification, or removal.
 */

namespace Base\IO;

class Dir
{
	protected $dir;
	
	const FILENAME = 0;
	const FILENAME_DESC = 1;
	const SIZE = 2;
	const SIZE_DESC = 3;
	
	function __construct ($path)
	{
		$this->dir = $path;
	}
	
	function create ($perms = 0644)
	{
		return mkdir($this->dir, $perms, true);
	}
	
	function remove ($recursive = true)
	{
		if ($recursive === true)
		{
			$dir = new RecursiveDirectoryIterator($this->dir);
			$files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);
			foreach ($files as $obj)
			{
				if ($obj->isDir())
				{
					rmdir($obj->getPathname());
				}
				else
				{
					unlink($obj->getPathname());
				}
			}
			
			// Finally remove the directory
			return rmdir($this->dir);
		}
		else
		{
			$dirs = new DirectoryIterator($this->dir);
			foreach ($dirs as $obj)
			{
				if ($obj->isFile())
				{
					unlink($obj->getPathname());
				}
			}
			return true;
		}
	}
	
	function exists ()
	{
		return file_exists($this->dir) && is_dir($this->dir);
	}
	
	function copy ($dest, $recursive = false)
	{
		$dir = new RecursiveDirectoryIterator($this->dir);
		$files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);
		foreach ($files as $obj)
		{
			$dest = $dest.'/'.$obj->getSubPathName();
			if ($obj->isDir())
			{
				mkdir($dest, $obj->getPerms());
			}
			else
			{
				copy($obj->getPathname(), $dest);
			}
		}
		return true;
	}
	
	function basename ()
	{
		return basename($this->dir);
	}
	
	function move ($dest)
	{
		return $this->copy($dest) && $this->remove(true);
	}
	
	function rename ($dest)
	{
		return rename($this->dir, dirname($this->dir).'/'.$dest);
	}
	
	function listAll ($sortBy = self::FILENAME)
	{
		$result = array();
		
		$files = new DirectoryIterator($this->dir);
		foreach ($files as $obj)
		{
			if ($obj->isFile())
			{
				$result[] = ['size' => $obj->getSize(), 'name' => $obj->getBasename()];
			}
		}
		
		switch ($sortBy)
		{
			case self::SIZE:
				usort($result, function ($row1, $row2) {
					return $row1['size'] < $row2['size'];
				});
				return $result;
			case self::SIZE_DESC:
				usort($result, function ($row1, $row2) {
					return $row1['size'] > $row2['size'];
				});
				return $result;
			case self::FILENAME_DESC:
				return array_reverse($result);
			case self::FILENAME:
			default:
				return $result;
		}
	}
	
	function getDir ()
	{
		return $this->dir;
	}
	
	function size ($recursive = false)
	{
		$size = 0;
		if ($recursive === true)
		{
			$dir = new RecursiveDirectoryIterator($this->dir);
			$files = new RecursiveIteratorIterator($dir);
		}
		else
		{
			$files = new DirectoryIterator($this->dir);
		}
		
		foreach ($files as $obj)
		{
			if ($obj->isFile())
			{
				$size += $obj->getSize();
			}
		}
		return $size;
	}
	
	public static function size ($path, $recursive = false)
	{
		$d = new Dir($path);
		return $d->size($recursive);
	}
}