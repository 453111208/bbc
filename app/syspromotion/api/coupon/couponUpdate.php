<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 更新优惠券信息
 */
final class syspromotion_api_coupon_couponUpdate {

    public $apiDescription = '更新优惠券信息';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'coupon_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'优惠券id'],
        );

        return $return;
    }

    /**
     *  更新优惠券信息
     * @param  array $apiData api数据
     * @return bool
     */
    public function couponUpdate($apiData)
    {
        return kernel::single('syspromotion_coupon')->saveCoupon($apiData);
    }

}

