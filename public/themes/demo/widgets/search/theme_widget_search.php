<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_search(&$setting)
{
    $data['def_key'] = app::get('search')->getConf('search_key');

    foreach($setting['top_link_title'] as $tk=>$tv){
        $data['search'][$tk]['top_link_title'] = $tv;
        $data['search'][$tk]['top_link_url'] = $setting['top_link_url'][$tk];
    }

    return $data;
}
?>
