<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class sysopen_shop_info
{
    public function getShopOpenInfo($shopId)
    {
        $keysModel = app::get('sysopen')->model('keys');
        $filter = ['shop_id'=>$shopId];
        $keys = $keysModel->getRow('*', $filter);
        return $keys;
    }

}
