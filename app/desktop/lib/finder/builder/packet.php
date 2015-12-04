<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_finder_builder_packet extends desktop_finder_builder_prototype{

    function main()
    {
        $pagedata['data'] = $this->getViews();
        /** 判断是否要显示归类视图 **/
        $pagedata['haspacket'] = $pagedata['data'] ? true : false;
        return view::make('desktop/finder/view/packet.html', $pagedata)->render();
    }
}
