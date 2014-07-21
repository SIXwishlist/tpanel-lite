<?php
// COMPLETE
namespace Base\Render;
use Base\Render;
use Base\Template as Engine;

class Template extends Render
{
	function render ()
	{
		$file = $this->getRenderFile().'.tpl';
		
		$tpl = Engine::fromFile($file);
		$tpl->setPath($this->path);
		
		if ($this->data !== null && is_array($this->data) && count($this->data) > 0)
		{
			foreach ($this->data as $key => $value)
			{
				$tpl->$key = $value;
			}
		}
		
		if ($this->layout !== null)
		{
			$tpl->setFile($this->file);
		}
		
		// Render the template
		$tpl->render();
	}
}
