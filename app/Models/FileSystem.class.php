<?php

namespace App\Models;
use Base\MVC\Model;
use Base\Arr;
use Base\Exception;
use Base\IO\Dir;
use Base\IO\File;
use Base\Filter;

class FileSystem extends Model
{
	protected $user;
	
	function init ()
	{
		$this->user = null;
	}
	
	function setUser ($username)
	{
		$this->user = $username;
	}
	
	protected function getUserDir ()
	{
		if ($this->user === null)
		{
			throw new Exception('User Account Undefined', 'No user account was specified in the FileSystem model');
		}
		return $this->Config->getUserDir().'/'.$this->user.'/';
	}
	
	function used ()
	{
		// true = recursive
		return Dir::getSize($this->getUserDir(), true);
	}
	
	protected function filterDir ($dir)
	{
		$dir = str_replace('..', '', $dir);
		$dir = str_replace('//', '/', $dir);
		return $dir;
	}
	
	function mkdir ($dir)
	{
		$dir = $this->filterDir($dir);
		$d = new Dir($this->getUserDir().$dir);
		return $d->create(0777);
	}
	
	function touch ($file)
	{
		$file = $this->filterDir($file);
		$f = new File($this->getUserDir().$file);
		return $f->create(0644);
	}
	
	function delete ($file)
	{
		$file = $this->getUserDir().$this->filterDir($file);
		if (File::isFile($file))
		{
			$f = new File($file);
			return $f->delete();
		}
		else
		{
			$d = new Dir($file);
			return $d->remove(true);
		}
	}
	
	function copyFile ($src, $dest)
	{
		$src = $this->getUserDir().$this->filterDir($src);
		$dest = $this->getUserDir().$this->filterDir($dest);
		if (File::isFile($src))
		{
			$f = new File($src);
			return $f->copy($dest);
		}
		else
		{
			$d = new Dir($src);
			return $d->copy($dest, true);
		}
	}
	
	function getFileMeta ($file)
	{
		$file = $this->getUserDir().$this->filterDir($file);
		if (File::isFile($file))
		{
			$f = new File($file);
			
			if ($f->exists())
			{
				return ['filename' => $f->basename(), 'type' => $f->mime(), 'size' => $f->sizeHuman()];
			}
			else
			{
				return false;
			}
		}
		else
		{
			$d = new Dir($file);
			if ($d->exists())
			{
				return ['filename' => $d->basename(), 'type' => 'directory', 'size' => '-'];
			}
			else
			{
				return false;
			}
		}
	}
	
	function moveFile ($src, $dest)
	{
		$src = $this->getUserDir().$this->filterDir($src);
		$dest = $this->getUserDir().$this->filterDir($dest);
		
		if (File::isFile($src))
		{
			$f = new File($src);
			return $f->move($dest);
		}
		else
		{
			$d = new Dir($src);
			return $d->move($dest);
		}
	}
	
	function renameFile ($src, $dest)
	{
		$src = $this->getUserDir().$this->filterDir($src);
		$dest = $this->getUserDir().$this->filterDir($dest);
		
		if (File::isFile($src))
		{
			$f = new File($src);
			return $f->rename($dest);
		}
		else
		{
			$d = new Dir($src);
			return $d->rename($dest);
		}
	}
	
	function saveFile ($file, $contents)
	{
		$file = $this->getUserDir().$this->filterDir($file);
		
		$f = new File($file);
		return $f->contents($contents);
	}
	
	function getContents ($file)
	{
		$file = $this->getUserDir().$this->filterDir($file);
		
		$f = new File($file);
		return $f->contents();
	}
	
	function upload ($dir, $request)
	{
		$dir = $this->getUserDir().$this->filterDir($dir);
		$info = $this->getUploadMeta($request);
		$f = new File($dir.'/'.$info['file']);
		
		return $f->upload($request->file('upload'));
	}
	
	function getUploadMeta ($request)
	{
		$file = $this->filterDir(Arr::get($request->file('upload'), 'filename', null));
		$size = Arr::get($request->file('upload'), 'size', null);
		return ['file' => $file, 'size' => $size];
	}
	
	function listFiles ($dir)
	{
		$dir = $this->getUserDir().$this->filterDir($dir);
		$d = new Dir($dir);
		
		if ($d->exists())
		{
			// Sort by filename
			return array_map(function ($item) {
				switch (strtolower(File::getExtension($item['name'])))
				{
					case 'zip':
					case 'tar':
					case 'gz':
					case 'tgz':
					case 'rar':
					case 'bz2':
					case 'cab':
						$item['icon'] = 'archive';
						break;
					case 'htm':
					case 'html':
						$item['icon'] = 'html';
						break;
					case 'jpg':
					case 'jpeg':
					case 'gif':
					case 'bmp':
					case 'png':
					case 'tiff':
					case 'tif':
					case 'svg':
						$item['icon'] = 'pic';
						break;
					default:
						$item['icon'] = 'file';
				}
				$item['isdir'] = $item['info']->isDir();
				
				return $item;
			}, $d->listAll(Dir::FILENAME));
		}
		else
		{
			return false;
		}
	}
	
	function destroy ()
	{
		$d = new Dir($this->getUserDir());
		return $d->remove(true);
	}
	
	function usageAsJSON ()
	{
		$colors = ['#8E44AD','#9B59B6','#C0392B','#E74C3C','#2980B9',
				   '#3498DB','#D35400','#E67E22','#27AE60','#2ECC71',
				   '#F39C12','#F1C40F','#16A085','#1ABC9C'];
		$d = new Dir($this->getUserDir());
		$files = $d->listAll(Dir::SIZE);
		$result = [];
		if (is_array($files) && count($files) > 0)
		{
			foreach ($files as $file)
			{
				if (File::isFile($d->getDir().'/'.$file['name']))
				{
					$size = $file['size'];
				}
				else
				{
					$subdir = new Dir($d->getDir().'/'.$file['name']);
					$size = $subdir->size(true);
				}
				$index = intval(($size / 1024 / 1024) * count($colors)) % count($colors);
				$result[] = ['label' => $file['name'].' ('.Filter::filesize($size).')', 'value' => $size, 'color' => $colors[$index]];
			}
		}
		return json_encode($result, true);
	}
}
