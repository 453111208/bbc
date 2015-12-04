<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_facades_redirect extends base_facades_facade
{

	/**
	 * Return the Request instance
	 * 
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */
    
    private static $__redirect;

    protected static function getFacadeAccessor() {
        if (!static::$__redirect)
        {
            static::$__redirect = new base_routing_redirector(url::instance());
        }
        return static::$__redirect;
    }
    
}
