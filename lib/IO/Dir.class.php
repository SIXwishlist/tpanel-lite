<?php

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
	const SIZE = 1;
	
	function __construct ($path)
	{
		$this->dir = $path;
	}
	
	function create ($perms = 0644)
	{
		return mkdir($this->dir, $perms, true);
	}
	
	function remove ($recursive = false)
	{
		
	}
	
	function exists ()
	{
	
	}
	
	function copy ($dest, $recursive = false)
	{
	
	}
	
	function basename ()
	{
	
	}
	
	function move ($dest)
	{
	
	}
	
	function rename ($dest)
	{
	
	}
	
	function listAll ($sortBy = self::FILENAME, $recursive = false)
	{
	
	}
	
	function getDir ()
	{
		return $this->dir;
	}
	
	function size ($recursive = false)
	{
	
	}
	
	public static function size ($path, $recursive = false)
	{
		$d = new Dir($path);
		return $d->size($recursive);
	}
}