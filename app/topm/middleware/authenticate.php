<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topm_middleware_authenticate
{

    public function __construct()
    {
        
    }

    public function handle($request, Clousure $next)
    {
        if( !userAuth::check() )
        {
            return redirect::action('topm_ctl_passport@signin');
        }
        return $next($request);
    }
}
