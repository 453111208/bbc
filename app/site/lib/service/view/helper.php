<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

class site_service_view_helper 
{
    function function_header($params)
    {
        return view::make('site/service/header.html')->render();
    }//End Function

    function function_wapheader($params)
    {
        return view::make('site/service/wapheader.html')->render();
    }//End Function

}//End Class
