<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_facades_url extends base_facades_facade
{
	/**
	 * Return the Request instance
	 *
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */

    private static $__url;

    protected static function getFacadeAccessor()
    {
        if (!static::$__url)
        {
            $routes = route::getRoutes();

            static::$__url = new base_routing_urlgenerator($routes, request::instance());
        }
        return static::$__url;
    }

}


