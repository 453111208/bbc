<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_facades_input extends base_facades_facade
{

	/**
	 * Get an item from the input data.
	 *
	 * This method is used for all request verbs (GET, POST, PUT, and DELETE)
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */

	public static function get($key = null, $default = null)
	{
        return request::input($key, $default);
	}

    protected static function getFacadeAccessor() {
        return request::instance();
    }
}
