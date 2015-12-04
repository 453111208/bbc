<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use Closure;
use BadMethodCallException;

trait base_support_traits_macro {

	/**
	 * The registered string macros.
	 *
	 * @var array
	 */
	protected static $macros = array();

	/**
	 * Register a custom macro.
	 *
	 * @param  string    $name
	 * @param  callable  $macro
	 * @return void
	 */
	public static function macro($name, $macro)
	{
		static::$macros[$name] = $macro;
	}

	/**
	 * Checks if macro is registered
	 *
	 * @param  string    $name
	 * @return boolean
	 */
	public static function hasMacro($name)
	{
		return isset(static::$macros[$name]);
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic($method, $parameters)
	{
		if (static::hasMacro($method))
		{
			if (static::$macros[$method] instanceof Closure)
			{
				return call_user_func_array(Closure::bind(static::$macros[$method], null, get_called_class()), $parameters);
			}
			else
			{
				return call_user_func_array(static::$macros[$method], $parameters);
			}
		}

		throw new BadMethodCallException("Method {$method} does not exist.");
	}

	/**
	 * Dynamically handle calls to the form builder.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		return static::__callStatic($method, $parameters);
	}

}

