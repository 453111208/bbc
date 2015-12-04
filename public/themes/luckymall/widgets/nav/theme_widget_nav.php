<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_nav($setting){

    $result = config::get('sitemenu');
    $cur_url = $_SERVER ['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $setting['max_leng'] = $setting['max_leng'] ? $setting['max_leng'] : 7;
    $setting['showinfo'] = $setting['showinfo'] ? $setting['showinfo'] : app::get('b2c')->_("更多");

    foreach($setting['urls'] as $key=>$val)
    {
        $ret[$key]['title'] = $val['title'];
        $ret[$key]['url'] = url::to($val['link']);
        if (request::url() == $ret[$key]['url'])
              $ret[$key]['hilight'] = true;
    }
    return $ret;
}
?>