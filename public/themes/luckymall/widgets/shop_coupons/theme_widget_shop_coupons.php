<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_shop_coupons( &$setting )
{
    if( !$setting['coupons'] ) return $setting;

    $params['coupon_id'] = implode(',',$setting['coupons']);
    $params['fields'] = "coupon_id,shop_id,coupon_name,deduct_money";
    $data = app::get('topc')->rpcCall('promotion.coupon.list.byid',$params);
    $shopIds = array_column($data, 'shop_id');
    $shopData = app::get('topc')->rpcCall('shop.get.list',['shop_id'=>$shopIds,'fields'=>'shop_id,shop_name,shop_logo']);
    $shop = array_bind_key($shopData,'shop_id');
    foreach( $data as &$row )
    {
        $row['deduct_money'] = intval($row['deduct_money']);
        $row['shop_logo'] = $shop[$row['shop_id']]['shop_logo'];
        $row['shop_name'] = $shop[$row['shop_id']]['shop_name'];
    }
    $setting['data'] = $data;
    return $setting;
}
?>
