<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class pam_facades_userAuth extends base_facades_facade
{
	/**
	 * Return the userAuth instance
	 *
	 * @var pam_auth_user;
	 */
    private static $__userAuth;

    protected static function getFacadeAccessor()
    {
        pamAccount::setAuthType('sysuser');
        if (!static::$__userAuth)
        {
            static::$__userAuth = new pam_auth_user();
        }
        return static::$__userAuth;
    }
}
