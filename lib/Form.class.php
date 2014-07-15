<?php

/**
 * Form
 *
 * Provides an encapsulation for data as well as a simplified way of generating
 * form fields (with Request-aware properties for reading submitted values upon
 * display).
 */

namespace Base;

class Form
{
	protected $request;
	
	function __construct ($request)
	{
		$this->request = $request;
	}
}