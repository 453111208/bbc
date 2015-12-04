<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_middleware_redirectIfFromWap
{
    public function __construct()
    {
    }

    public function handle($request, Clousure $next)
    {
        $wapIsOpen = app::get('sysconf')->getConf('sysconf_setting.wap_isopen');

        if(base_mobiledetect::isMobile() && $_COOKIE['browse'] != 'pc' && $wapIsOpen)
        {
            return redirect::route('topm');
        }
        return $next($request);
    }
}
