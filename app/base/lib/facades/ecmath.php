<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */



class base_facades_ecmath extends base_facades_facade
{
    /**
     * Return the View instance
     * 
     * @var \Symfony\Component\HttpFoundation\Request;
     */
    
    private static $__ecmath;
    
    protected static function getFacadeAccessor()
    {
        if (!static::$__ecmath)
        {
            return kernel::single('ectools_math');
        }
        return static::$__ecmath;
    }
}
