<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class site_facades_seo extends base_facades_facade
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
            static::$__theme = new site_seo_base();
        }
        return static::$__theme;
    }

}
