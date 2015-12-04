<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */



class base_facades_hash extends base_facades_facade
{
	/**
	 * Return the View instance
	 * 
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */
    
    private static $__hasher;
    
    protected static function getFacadeAccessor()
    {
        if (!static::$__hasher)
        {
            return kernel::single('base_hashing_hasher_bcrypt');
        }
        return static::$__hasher;
    }
}
