<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 删除单条优惠券信息
 */
final class syspromotion_api_coupon_couponDelete {

    public $apiDescription = '删除单条优惠券信息';

    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'店铺ID必填'],
            'coupon_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'优惠券ID必填'],
        );

        return $return;
    }

    /**
     * 根据优惠券ID删除优惠券
     * @param  array $couponId 优惠券id
     * @return bool
     */
    public function couponDelete($params)
    {
        return kernel::single('syspromotion_coupon')->deleteCoupon($params);
    }

}

