<?php


class site_service_view_menu {
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

    function function_menu(){
        $html = array();
        $shopUrl = url::action('topc_ctl_default@index');
        $shopWapUrl = url::action('topm_ctl_default@index');
        $html[] = "<a href='$shopUrl' target='_blank'>浏览商城</a>";
        $html[] = "<a href='$shopWapUrl' target='_blank'>手机商城</a>";

        return $html;

    }
}
