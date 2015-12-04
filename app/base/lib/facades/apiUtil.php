<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_facades_apiUtil extends base_facades_facade
{
	/**
	 * Return the View instance
	 *
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */

    private static $__apiUtil;

    protected static function getFacadeAccessor()
    {
        if (!static::$__apiUtil)
        {
            static::$__apiUtil = new base_prism_util();
        }
        return static::$__apiUtil;
    }
}
