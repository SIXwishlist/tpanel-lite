<?php
// COMPLETE
/**
 * Validator
 *
 * Validates a set of data by analyzing values in an associative array and
 * subjecting them to various validators and returning a Validator which
 * returns whether validation was successful and what errors exist.
 */

namespace Base;

class Validator
{
	const MSG_REQUIRED = '%s is a required field';
	const MSG_RANGE1 = '%s must be a value between %d and %d';
	const MSG_RANGE2 = '%s cannot be lower than %d';
	const MSG_RANGE3 = '%s cannot be higher than %d';
	const MSG_NUMERIC = '%s is a numeric field';
	const MSG_REGEX = '%s did not match the required pattern';
	const MSG_CALLBACK = '%s did not pass validation';
	const MSG_CURRENCY = '%s must be entered as currency';
	const MSG_EMAIL = '%s contains an invalid e-mail address';
	const MSG_TELEPHONE = '%s is not a valid phone number';
	const MSG_LENGTH1 = '%s must be at least %d characters long';
	const MSG_LENGTH2 = '%s must be at most %d characters long';
	const MSG_LENGTH3 = '%s must be between %d and %d characters long';
	const MSG_DECIMAL = '%s must be a decimal';
	const MSG_EQUALS = '%s must be "%s"';
	const MSG_SAME = '"%s" and "%s" are not the same';
	
	protected $data;
	protected $messages;
	protected $labels;
	
	private function __construct ($data)
	{
		$this->data = $data;
		$this->messages = array();
		$this->labels = null;
	}
	
	function labels ($data)
	{
		$this->labels = $data;
		
		return $this;
	}
	
	protected function label ($key)
	{
		if (isset($this->labels[$key]))
		{
			return $this->labels[$key];
		}
		else
		{
			return Filter::readable($key);
		}
	}
	
	function error ($index)
	{
		return $this->messages[$index];
	}
	
	function errors ()
	{
		return $this->messages;
	}
	
	function success ()
	{
		return is_array($this->messages) && count($this->messages) == 0;
	}
	
	public static function evaluate ($data)
	{
		return new Validator($data);
	}
	
	protected function add_message ($message, $generic, $params)
	{
		if ($message === null)
		{
			array_push($this->messages, vsprintf($generic, $params));
		}
		else
		{
			array_push($this->messages, $message);
		}
	}
	
	function required ($key, $msg = null)
	{
		if (is_array($key))
		{
			if (is_array($msg))
			{
				foreach ($key as $field)
				{
					$this->required($field, array_shift($msg));
				}
			}
			else
			{
				foreach ($key as $field)
				{
					$this->required($field, $msg);
				}
			}
		}
		else
		{
			if (!isset($this->data[$key]) || strlen(trim($this->data[$key])) < 1)
			{
				$this->add_message($msg, self::MSG_REQUIRED, array($this->label($key)));
			}
		}
		
		return $this;
	}
	
	protected function valid_phone ($str, $digits)
	{
		$str = preg_replace('/[\s\-\.\(\)]/', '', $str);
		return ctype_digit($str) && strlen($str) >= $digits;
	}
	
	function phone ($key, $digits = 10, $msg = null)
	{
		if ($this->has_input($key) && !$this->valid_phone($this->data[$key], $digits))
		{
			$this->add_message($msg, self::MSG_TELEPHONE, array($this->label($key)));
		}
		return $this;
	}
	
	function range ($key, $low, $high = false, $msg = null)
	{
		if ($low !== false && $high !== false)
		{
			if ($this->has_input($key) && ($this->data[$key] < $low || $this->data[$key] > $high))
			{
				$this->add_message($msg, self::MSG_RANGE1, array($this->label($key), $low, $high));
			}
		}
		elseif ($low !== false)
		{
			if ($this->has_input($key) && $this->data[$key] < $low)
			{
				$this->add_message($msg, self::MSG_RANGE2, array($this->label($key), $low));
			}
		}
		else
		{
			if ($this->has_input($key) && $this->data[$key] > $high)
			{
				$this->add_message($msg, self::MSG_RANGE3, array($this->label($key), $high));
			}
		}
		
		return $this;
	}
	
	function decimal ($key, $msg = null)
	{
		if ($this->has_input($key) && !preg_match('/^([0-9]+\.[0-9]+|[0-9]+)$/', $this->data[$key]))
		{
			$this->add_message($msg, self::MSG_DECIMAL, array($this->label($key)));
		}
		return $this;
	}
	
	function numeric ($key, $msg = null)
	{
		if (is_array($key))
		{
			if (is_array($msg))
			{
				foreach ($key as $field)
				{
					$this->numeric($field, array_shift($msg));
				}
			}
			else
			{
				foreach ($key as $field)
				{
					$this->numeric($field, $msg);
				}
			}
		}
		else
		{
			if ($this->has_input($key) && !ctype_digit($this->data[$key]))
			{
				$this->add_message($msg, self::MSG_NUMERIC, array($this->label($key)));
			}
		}
		
		return $this;
	}
	
	function regex ($key, $pattern, $msg = null)
	{
		if ($this->has_input($key) && !preg_match($pattern, $this->data[$key]))
		{
			$this->add_message($msg, self::MSG_REGEX, array($this->label($key)));
		}
		
		return $this;
	}
	
	function callback ($key, $callback, $msg = null)
	{
		if ($this->has_input($key) && !call_user_func($callback, $this->data[$key]))
		{
			$this->add_message($msg, self::MSG_CALLBACK, array($this->label($key)));
		}
		
		return $this;
	}
	
	function email ($key, $msg = null)
	{
		if ($this->has_input($key) && filter_var($this->data[$key], FILTER_VALIDATE_EMAIL) === false)
		{
			$this->add_message($msg, self::MSG_EMAIL, array($this->label($key)));
		}
		
		return $this;
	}
	
	function currency ($key, $msg = null)
	{
		if ($this->has_input($key) && !preg_match('/^[0-9]+($|\.[0-9]{2}$)/', $this->data[$key]))
		{
			$this->add_message($msg, self::MSG_CURRENCY, array($this->label($key)));
		}
		
		return $this;
	}
	
	function has_input ($key)
	{
		return isset($this->data[$key]) && strlen($this->data[$key]) > 0;
	}
	
	function same ($key1, $key2, $msg = null)
	{
		if ($this->has_input($key1) || $this->has_input($key2))
		{
			if (strcmp($this->data[$key1], $this->data[$key2]) !== 0)
			{
				$this->add_message($msg, self::SAME, array($this->label($key1), $this->label($key2)));
			}
		}
		
		return $this;
	}
	
	function length ($key, $low, $high = false, $msg = null)
	{
		if ($high === false)
		{
			if ($this->has_input($key) && strlen($this->data[$key]) < $low)
			{
				$this->add_message($msg, self::MSG_LENGTH1, array($this->label($key), $low));
			}
		}
		elseif ($low === false)
		{
			if ($this->has_input($key) && strlen($this->data[$key]) > $high)
			{
				$this->add_message($msg, self::MSG_LENGTH2, array($this->label($key), $high));
			}
		}
		else
		{
			if ($this->has_input($key) && (strlen($this->data[$key]) > $high || strlen($this->data[$key]) < $low))
			{
				$this->add_message($msg, self::MSG_LENGTH3, array($this->label($key), $low, $high));
			}
		}
		
		return $this;
	}
	
	function equals ($key, $value, $msg = null)
	{
		if ($this->has_input($key) && strcmp($this->data[$key], $value) != 0)
		{
			$this->add_message($msg, self::MSG_EQUALS, array($this->label($key), $value));
		}
		
		return $this;
	}
	
	function assert ($case, $msg)
	{
		if ($case !== true)
		{
			$this->add_message($msg, 'Assertion failed in Validator', array());
		}
		return $this;
	}
}
