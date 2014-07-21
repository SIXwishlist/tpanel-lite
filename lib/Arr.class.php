<?php
// COMPLETE
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
}