<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_ctl_shop_shopinfo extends topshop_controller
{
	public function index()
	{
		$this->contentHeaderTitle = '店铺入驻信息';
        //获取商家店铺信息(shop、shop_info、brand)
        $shopId = $this->shopId;
        $params = array(
            'shop_id' => $shopId,
            'fields' =>'cat.cat_name,cat.cat_id,brand.brand_name,brand.brand_id,info',
        );
        $shopdata = app::get('topshop')->rpcCall('shop.get.detail',$params);
		$pagedata['shop'] = $shopdata['shop'];
        if($shopdata['shop_info'])
        {
            $pagedata['shop_info'] = $shopdata['shop_info'];
            $pagedata['shop_info']['establish_date'] = date('Y-m-d',$pagedata['shop_info']['establish_date']);
            $pagedata['shop_info']['license_indate'] = date('Y-m-d',$pagedata['shop_info']['license_indate']);
        }

		$pagedata['shopBrandInfo'] = $shopdata['brand'];

        //获取店铺类目及类目费率
        if($shopdata['shop']['shop_type']!='self')
        {
            $pagedata['shopCatInfo'] = app::get('topshop')->rpcCall('shop.get.cat.fee',array('shop_id'=>$shopId));
        }

		return $this->page('topshop/shop/shopapplyinfo.html', $pagedata);
	}
}
