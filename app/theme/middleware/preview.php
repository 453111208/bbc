<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class theme_middleware_preview
{
    public function __construct()
    {
    }

    public function handle($request, Clousure $next)
    {
        if (isset($_COOKIE['site']['preview'])&&$_COOKIE['site']['preview']=='true')
        {
            config::set('cache.enabled', false);
            theme::preview();
        }
        return $next($request);
    }
}
