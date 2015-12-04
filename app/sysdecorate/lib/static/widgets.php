<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class sysdecorate_static_widgets extends base_facades_facade
{

	/**
	 * Return the Request instance
	 *
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */

    private static $__request;

    protected static function getFacadeAccessor() {
        if (!static::$__request)
        {
            static::$__request = new sysdecorate_widgets();

        }
        return static::$__request;
    }
}



