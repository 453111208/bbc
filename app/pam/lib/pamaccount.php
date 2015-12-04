<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class pam_pamaccount extends base_facades_facade
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
            static::$__request = new pam_account();

        }
        return static::$__request;
    }
}

