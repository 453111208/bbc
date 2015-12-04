<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class sysopen_shop_conf extends system_prism_init_base
{
    public function getShopConf($shopId)
    {
        $keysModel = app::get('sysopen')->model('shopconf');
        $filter = ['shop_id'=>$shopId];
        $confs = $keysModel->getRow('*', $filter);
        return $confs;
    }

    public function setShopConf($shopId, $developMode = "PRODUCT")
    {
        $keysModel = app::get('sysopen')->model('shopconf');
        $saveDataFormat = [
            'shop_id' => $shopId,
            'develop_mode' => $developMode == "DEVELOP" ? "DEVELOP" : "PRODUCT",
            ];
        $keysModel->save($saveDataFormat);
        return true;
    }
}
