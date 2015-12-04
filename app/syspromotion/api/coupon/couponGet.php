<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取单条优惠券数据
 */
final class syspromotion_api_coupon_couponGet {

    public $apiDescription = '获取单条优惠券数据';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'coupon_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'优惠券id'],
            'coupon_itemList' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'优惠券的商品'],
        );

        return $return;
    }

    /**
     *  获取单条优惠券信息
     * @param  array $params 筛选条件数组
     * @return array         返回一条优惠券信息
     */
    public function couponGet($params)
    {
        $couponInfo = kernel::single('syspromotion_coupon')->getCoupon($params['coupon_id']);
        $couponInfo['valid'] = $this->__checkValid($couponInfo);
        if($params['coupon_itemList'])
        {
            $couponItems = kernel::single('syspromotion_coupon')->getCouponItems($params['coupon_id']);
            $couponInfo['itemsList'] = $couponItems;
        }
        return $couponInfo;
    }

    // 检查当前优惠券是否可用
    private function __checkValid(&$couponInfo)
    {
        $now = time();
        if( ($couponInfo['coupon_status']=='agree') && ($couponInfo['canuse_start_time']>$now) && ($couponInfo['canuse_end_time']>$now) )
        {
            return true;
        }
        return false;
    }

}

