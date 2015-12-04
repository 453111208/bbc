<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */



class base_facades_rpc extends base_facades_facade
{
	/**
	 * Return the View instance
	 * 
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */
    
    private static $__rpc;
    
    protected static function getFacadeAccessor()
    {
        if (!static::$__rpc)
        {
            static::$__rpc = new base_rpc_client();
        }
        return static::$__rpc;
    }
}
