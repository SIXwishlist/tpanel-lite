<?php

namespace Base\Render;
use Base\Render;

class PHTML extends Render
{
	function render ()
	{
		$file = $this->getRenderFile().'.phtml';
		$this->renderPHTML($file);
	}
	
	protected function renderPHTML ($_file)
	{
		extract($this->data, EXTR_SKIP);
		require($_file);
	}
}