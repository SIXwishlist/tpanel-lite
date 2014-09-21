<?php
/**
 * Arr
 *
 * A set of helper methods for assisting with common array tasks not natively
 * supported in PHP or not easily written.
 */

namespace Base;

class Arr
{
	public static function filter ($data, $keys)
	{
		return array_intersect_key($data, array_flip($keys));
	}
	
	public static function filterNonNull ($data, $keys)
	{
		$result = array();
		foreach ($keys as $key)
		{
			if (isset($data[$key]) && $data[$key] !== '' && $data[$key] !== null)
			{
				$result[$key] = $data[$key];
			}
		}
		return $result;
	}
	
	public static function get ($data, $key, $default = null)
	{
		if (isset($data[$key]))
		{
			return $data[$key];
		}
		else
		{
			return $default;
		}
	}
}