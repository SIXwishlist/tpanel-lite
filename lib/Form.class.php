<?php
// COMPLETE
/**
 * Form
 *
 * Provides an encapsulation for data as well as a simplified way of generating
 * form fields (with Request-aware properties for reading submitted values upon
 * display).
 */

namespace Base;
use Base\UI\HTML;

class Form
{
	protected $request;
	
	function __construct ($request)
	{
		$this->request = $request;
	}
	
	function text ($name, $attr = null)
	{
		return $this->input('text', $name, $attr);
	}
	
	function input ($type, $name, $attr = null)
	{
		if ($attr === null)
		{
			$attr = [];
		}
		$attr['name'] = $name;
		$attr['id'] = $name;
		$attr['type'] = $type;
		return HTML::tag('input', $attr);
	}
	
	function password ($name, $attr = null, $opts = null)
	{
		return HTML::password($name, $attr, $opts);
	}
	
	function submit ($name, $attr = null)
	{
		return HTML::submit($name, $attr);
	}
}