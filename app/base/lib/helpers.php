<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


if ( ! function_exists('abort'))
{
	/**
	 * Throw an HttpException with the given data.
	 *
	 * @param  int     $code
	 * @param  string  $message
	 * @param  array   $headers
	 * @return void
	 *
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	function abort($code, $message = '', array $headers = array())
	{
		return kernel::abort($code, $message, $headers);
	}
}

if ( ! function_exists('action'))
{
	/**
	 * Generate a URL to a controller action.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @return string
	 */
	function action($name, $parameters = array())
	{
		return url::action($name, $parameters);
	}
}

if ( ! function_exists('back'))
{
	/**
	 * Create a new redirect response to the previous location.
	 *
	 * @param  int    $status
	 * @param  array  $headers
	 * @return \Illuminate\Http\RedirectResponse
	 */
	function back($status = 302, $headers = array())
	{
		return redirect::back($status, $headers);
	}
}

if ( ! function_exists('hash'))
{
	/**
	 * Hash the given value.
	 *
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	function hash($value, $options = array())
	{
		return hash::make($value, $options);
	}
}


