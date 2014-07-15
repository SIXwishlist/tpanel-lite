<?php
// COMPLETE
/**
 * Template
 *
 * Simple template engine that compiles to PHP and eval's the result.
 */

namespace Base;

class Template
{
	const PHP_START = '<?php ';
	const PHP_END = ' ?>';
	protected $code;
	protected $output;
	protected $data = array();
	protected $callbacks = array();
	
	function __set ($k, $value)
	{
		$this->data[$k] = $value;
	}

	function __get ($var)
	{
		return $this->data[$var];
	}

	function __isset ($var)
	{
		return isset($this->data[$var]);
	}
	
	function __call ($name, $args)
	{
		if (isset($this->callbacks[$name]))
		{
			return call_user_func_array($this->callbacks[$name], $args);
		}
		else
		{
			return null;
		}
	}

	protected function __construct ($code)
	{
		$this->code = explode("\n", $code);
		$this->output = '';
		$this->addFunction('theme', array($this, 'theme'));
		$this->addFunction('url', array($this, 'url'));
		$this->addFunction('filter', array($this, 'filter'));
	}

	protected function build ()
	{
		if (count($this->code) > 0)
		{
			foreach ($this->code as $line)
			{
				$this->output .= $this->compileLine($line);
			}
		}
	}

	protected function getOutput ()
	{
		return $this->output;
	}

	protected function compileLine ($line)
	{
		// {{ @statement: }}
		$line = preg_replace('/\{\{\s*@(.+?)\:\s*\}\}/', self::PHP_START.'print \$this->filter(\$this->$1());'.self::PHP_END, $line);

		// {{ @statement(...) }}
		$line = preg_replace('/\{\{\s*@(.+?)\((.+?)\)\s*\}\}/', self::PHP_START.'print \$this->filter(\$this->$1($2));'.self::PHP_END, $line);
		
		// {{ @statement }}
		$line = preg_replace('/\{\{\s*@(.+?)\s*\}\}/', self::PHP_START.'print \$this->filter(\$$1);'.self::PHP_END, $line);

		// { foreach $x as $y } or { foreach $x -> $y }
		$line = preg_replace('/\{\s*foreach\s+(.+?)\s+(as|\-\>)\s+(.+?)\s*\}/i', self::PHP_START.'if (isset($1) && is_array($1) && count($1) > 0) foreach ($1 as $3) {'.self::PHP_END, $line);

		// { end }
		$line = preg_replace('/\{\s*end\s*\}/i', self::PHP_START.'}'.self::PHP_END, $line);
		
		// { if $x }
		$line = preg_replace('/\{\s*if\s+([A-Za-z0-9\_]+?)\s*\}/i', self::PHP_START.'if (isset($1)) {'.self::PHP_END, $line);

		// { if .. }
		$line = preg_replace('/\{\s*if\s+(.+?)\s*\}/i', self::PHP_START.'if ($1) {'.self::PHP_END, $line);
		
		// { else }
		$line = preg_replace('/\{\s*else\s*\}/i', self::PHP_START.'} else {'.self::PHP_END, $line);

		// { elseif .. }
		$line = preg_replace('/\{\s*elseif\s+(.+?)\s*\}/i', self::PHP_START.'} elseif ($1) {'.self::PHP_END, $line);
		
		// {! ... }
		$line = preg_replace('/\{\!\s*(.+?)\s*\}/i', self::PHP_START.'$1'.self::PHP_END, $line);

		return $line;
	}
	
	protected function url ($text)
	{
		return Path::web($text);
	}
	
	protected function theme ($text)
	{
		return Path::theme($text);
	}
	
	protected function filter ($text)
	{
		return Filter::html($text);
	}

	public static function compile ($code)
	{
		$p = new Template($code);
		$p->build();
		return $p;
	}
	
	function addFunction ($name, $callback)
	{
		$this->callbacks[$name] = $callback;
	}
	
	function toPHP ()
	{
		return $this->output;
	}
	
	function render ()
	{
		extract($this->data, EXTR_SKIP);
		eval('?>'.$this->toPHP());
	}
	
	function _render ()
	{
		extract($this->data, EXTR_SKIP);
		ob_start();
		eval('?>'.$this->toPHP());
		$c = ob_get_contents();
		ob_end_clean();
		return $c;
	}
	
	public static function fromFile ($file)
	{
		$contents = file_get_contents($file);
		return self::compile($contents);
	}
}