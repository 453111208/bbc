<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_facades_response extends base_facades_facade
{
    
    use base_support_traits_macro;
   
	/**
	 * Return the Response instance
	 * 
	 * @var \Symfony\Component\HttpFoundation\Response;
	 */
    private static $__response;

    //	use MacroableTrait;

	/**
	 * Return a new response from the application.
	 *
	 * @param  string  $content
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Illuminate\Http\Response
	 */
	public static function make($content = '', $status = 200, array $headers = array())
	{
		return new base_http_response($content, $status, $headers);
	}

	/**
	 * Return a new JSON response from the application.
	 *
	 * @param  string|array  $data
	 * @param  int    $status
	 * @param  array  $headers
	 * @param  int    $options
	 * @return \Illuminate\Http\JsonResponse
	 */
	public static function json($data = array(), $status = 200, array $headers = array(), $options = 0)
	{
        /*
		if ($data instanceof ArrayableInterface)
		{
			$data = $data->toArray();
		}
        */

		return new base_http_response_json($data, $status, $headers, $options);
	}

	/**
	 * Return a new view response from the application.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Illuminate\Http\Response
	 */
	public static function view($view, $data = array(), $status = 200, array $headers = array())
	{
		return static::make(view::make($view, $data), $status, $headers);
	}
    
}
