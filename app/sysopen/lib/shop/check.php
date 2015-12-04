<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class sysopen_shop_check extends system_prism_init_base
{
    public function checkLogin($shopId, $key)
    {
        $shopKey = kernel::single('sysopen_shop_info')->getShopOpenInfo($shopId);
        $shopConf = kernel::single('sysopen_shop_conf')->getShopConf($shopId);
        if($shopConf['develop_mode'] != "DEVELOP")
        {
            throw new LogicException('请开启开发者模式');
        }
        return null;
    }

}
