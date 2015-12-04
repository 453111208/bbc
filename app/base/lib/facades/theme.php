<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_facades_theme extends base_facades_facade
{

	/**
	 * Return the Request instance
	 * 
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */
    
    private static $__theme;

    protected static function getFacadeAccessor() {
        if (!static::$__theme)
        {
            static::$__theme = new theme_theme(view::getView());
        }
        return static::$__theme;
    }
    
}
