<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_middleware_authenticate
{

    public function __construct()
    {
        
    }

    public function handle($request, Clousure $next)
    {
        
        // 检测是否登录
        if( !userAuth::check() )
        {
            return redirect::action('topc_ctl_passport@signin');
        }
        return $next($request);
    }
}
