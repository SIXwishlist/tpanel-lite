<?php

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
	protected $method = 'post';
	protected $request;
	protected $responsive = true;
	protected $data = null;
	
	function __construct ($request)
	{
		$this->request = $request;
	}
	
	function assign ($data)
	{
		$this->data = $data;
	}
	
	function setResponsive ($r)
	{
		$this->responsive = $r === true;
	}
	
	function isResponsive ()
	{
		return $this->responsive;
	}
	
	function open ()
	{
		return HTML::open('form', ['method' => $this->method]);
	}
	
	function close ()
	{
		return HTML::close('form');
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
		$attr['type'] = $type;
		$attr['name'] = $name;
		$attr['id'] = $name;
		if ($this->responsive && $this->request->isPost())
		{
			$attr['value'] = $this->request->post($name);
		}
		elseif ($this->data !== null && isset($this->data[$name]))
		{
			$attr['value'] = $this->data[$name];
		}
		return HTML::tag('input', $attr);
	}
	
	function listbox ($name, $items, $selected = null, $attr = null)
	{
		if (!isset($attr['rows']))
		{
			$attr['rows'] = 5;
		}
		return $this->combo($name, $items, $selected, $attr);
	}
	
	function radio ($name, $value, $attr = null)
	{
		$attr += ['type' => 'radio'];
		return $this->checkedItem($name, $value, $attr);
	}
	
	function checkbox ($name, $attr = null)
	{
		$attr += ['type' => 'checkbox'];
		return $this->hiddenCheckbox($name, '0').$this->checkedItem($name, '1', $attr);
	}
	
	protected function hiddenCheckbox ($name, $value)
	{
		return HTML::tag('input', ['type' => 'hidden', 'name' => $name, 'value' => $value]);
	}
	
	protected function checkedItem ($name, $value, $attr = null)
	{
		$attr += ['name' => $name, 'id' => $name, 'value' => $value];
		if ($this->responsive && $this->request->isPost() && $this->request->post($name) !== null)
		{
			$val = $this->request->post($name);
			if (is_array($val) && in_array($value, $val))
			{
				$attr['checked'] = 'checked';
			}
			elseif (strcmp($val, $value) === 0)
			{
				$attr['checked'] = 'checked';
			}
			elseif (isset($attr['checked']))
			{
				unset($attr['checked']);
			}
		}
		return HTML::tag('input', $attr);
	}
	
	function combo ($name, $items, $selected = null, $attr = null)
	{
		$attr += ['name' => $name, 'id' => $name];
		$result = HTML::open('select', $attr);
		
		if ($this->responsive && $this->request->isPost())
		{
			$selected = $this->request->post($name);
		}
		
		if (is_array($items) && count($items) > 0)
		{
			foreach ($items as $value => $contents)
			{
				$optAttr = ['value' => $value];
				if ($selected !== null)
				{
					if (is_string($selected) && strcmp($selected, $value) === 0)
					{
						$optAttr['selected'] = 'selected';
					}
					elseif (is_array($selected) && in_array($value, $selected))
					{
						$optAttr['selected'] = 'selected';
					}
				}
				$result .= HTML::tag('option', $optAttr, Filter::html($contents));
			}
		}
		
		$result .= HTML::close('select');
		return $result;
	}
	
	function password ($name, $attr = null)
	{
		return $this->input('password', $name, $attr);
	}
	
	function textarea ($name, $content = null, $attr = null)
	{
		if ($attr === null)
		{
			$attr = [];
		}
		$attr['name'] = $name;
		$attr['id'] = $name;
		if ($this->responsive && $this->request->isPost())
		{
			$content = $this->request->post($name);
		}
		return HTML::tag('textarea', $attr, Filter::html($content));
	}
	
	function submit ($name, $attr = null)
	{
		return HTML::submit($name, $attr);
	}
}