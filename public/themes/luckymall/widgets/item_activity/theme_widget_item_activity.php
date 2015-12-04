<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_item_activity( &$setting )
{
    if( !$setting['activity'] ) return $setting;

    $params['id'] = implode(',',$setting['activity']);
    $params['status'] = 'agree';
    $params['fields'] = "title,item_id,item_default_image,activity_price,price,activity_tag";
    $data = app::get('desktop')->rpcCall('promotion.activity.item.list',$params);
    $setting['data'] = $data['list'];
    return $setting;
}
?>
