<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class sysopen_oauth
{
    public static function checkShopOauth($oauth)
    {
        if(! $oauth['data']['shop_id'] > 0)
        {
            throw new LogicException('店铺id获取失败');
        }
    }

    public static function checkShopLogin($shop)
    {
        if(!$shop['data']['shop_id'] > 0)
        {
            throw new LogicException('找不到该用户对应的商铺');
        }

    }
}

