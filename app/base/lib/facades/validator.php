<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */



class base_facades_validator extends base_facades_facade
{
    /**
     * Return the View instance
     * 
     * @var \Symfony\Component\HttpFoundation\Request;
     */
    
    private static $__validator;
    
    protected static function getFacadeAccessor()
    {
        if (!static::$__validator)
        {
            static::$__validator = new base_validator_factory();
        }
        return static::$__validator;
    }
}
