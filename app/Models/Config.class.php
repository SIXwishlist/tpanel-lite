<?php

namespace App\Models;
use Base\MVC\Model;

class Config extends Model
{
	protected $file;
	
	function init ()
	{
		$this->file = App::Data('tpanel.conf')->getFullPath();
	}
	
	function store ($data)
	{
	
	}
	
	function toArray ()
	{
	
	}
}