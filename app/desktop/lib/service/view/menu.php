<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_service_view_menu{
    function function_menu(){
        //$html[] = "<a href='?ctl=shoprelation&act=index&p[0]=apply'>网店邻居</a>";
        $html[] = "<a href='?app=desktop&ctl=appmgr&act=index'>".app::get('desktop')->_('应用中心')."</a>";
        $html[] = "<a href='?ctl=adminpanel'>".app::get('desktop')->_('控制面板')."</a>";
        $html[] = "<a href='?ctl=dashboard&act=index'>".app::get('desktop')->_('桌面')."</a>";
        return $html;
    }
}
