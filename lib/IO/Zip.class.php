<?php

/**
 * Zip
 *
 * Encapsulates SPL's ZipArchive class for more natural file access to ZIP
 * files.
 */

namespace Base\IO;
use \ZipArchive;

class Zip
{
	protected $zip;
	
	function __construct ($file, $accessMode = 'r')
	{
		$this->zip = new ZipArchive();
		if ($accessMode === 'w')
		{
			$this->zip->open($file, ZipArchive::OVERWRITE);
		}
		else
		{
			$this->zip->open($file);
		}
	}
	
	function addDirectory ($dir, $recursive = false)
	{
		if ($recursive === true)
		{
			$files = Dir::iterate($dir, true);
			foreach ($files as $f)
			{
				if (!$files->isDot())
				{
					if ($f->isDir())
					{
						$this->zip->addEmptyDir($files->getSubPathName());
					}
					else
					{
						$this->zip->addFile($f->getPathname(), $files->getSubPathName());
					}
				}
			}
		}
		else
		{
			$files = Dir::iterate($dir, false);
			foreach ($files as $f)
			{
				if ($f->isDir())
				{
					$this->zip->addEmptyDir('');
				}
				else
				{
					$this->zip->addFile($f->getPathname());
				}
			}
		}
	}
	
	function close ()
	{
		return $this->zip->close();
	}
	
	function extractAll ($path)
	{
		return $this->zip->extractTo($path);
	}
	
	function listAll ($metadata = false)
	{
		$files = [];
		if ($metadata === true)
		{
			for ($j=0;$j<$this->zip->numFiles;$j++)
			{
				$files[] = $this->zip->statIndex($j);
			}
		}
		else
		{
			for ($j=0;$j<$this->zip->numFiles;$j++)
			{
				$files[] = $this->zip->getNameIndex($j);
			}
		}
		return $files;
	}
}