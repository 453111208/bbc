<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取多条优惠券列表
 */
final class syspromotion_api_coupon_couponListById {

    public $apiDescription = '根据优惠券ID,获取优惠券列表';

    public function getParams()
    {
        $return['params'] = array(
            'coupon_id' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'1,2,3', 'description'=>'优惠券id'],
            'fields'    => ['type'=>'field_list', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>'coupon_id,coupon_name'],
        );
        return $return;
    }

    /**
     * 获取优惠券列表
     */
    public function getList($params)
    {
        $couponId = explode(',',$params['coupon_id']);
        $objMdlCoupon = app::get('syspromotion')->model('coupon');
        $couponData = $objMdlCoupon->getList($params['fields'], ['coupon_id'=>$couponId]);
        return $couponData;
    }

}

