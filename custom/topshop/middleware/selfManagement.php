<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_middleware_selfManagement
{

    public function handle($request, Clousure $next)
    {

        //获取shopInfo
        $sellerId = pamAccount::getAccountId();
        $shopId = app::get('topshop')->rpcCall('shop.get.loginId',array('seller_id'=>$sellerId),'seller');

        //获取商铺信息
        $requestParams = ['shop_id'=>$shopId, 'fields'=>'shop_id,shop_name,shop_type'];
        $shopInfo = app::get('topshop')->rpcCall('shop.get', $requestParams);

        if($shopInfo['shop_type'] != 'self')
        {
            return redirect::action('topshop_ctl_index@onlySelfManagement');
        }



        return $next($request);
    }

}

