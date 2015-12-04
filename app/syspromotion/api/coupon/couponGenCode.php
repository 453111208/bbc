<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 生成优惠券号码
 */
final class syspromotion_api_coupon_couponGenCode {

    public $apiDescription = '生成优惠券号码';

    public function getParams()
    {
        $return['params'] = array(
            'grade_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户等级id'],
            'coupon_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'优惠券id'],
            'gen_quantity' => ['type'=>'int','valid'=>'required', 'default'=>'1', 'example'=>'', 'description'=>'生成数量'],
            'old_quantity' => ['type'=>'int','valid'=>'', 'default'=>'0', 'example'=>'', 'description'=>'原有此优惠券数量'],
        );

        return $return;
    }

    /**
     *  生成优惠券号码
     * @param  array $params 筛选条件数组
     * @return array
     */
    public function couponGenCode($params)
    {
        $gen_quantity = $params['gen_quantity'];
        $apiData = array(
            'coupon_id'=>$params['coupon_id'],
            'grade_id'=>$params['grade_id'],
            'old_quantity'=>$params['old_quantity'],
        );
        return kernel::single('syspromotion_coupon')->_makeCouponCode($apiData, $gen_quantity);
    }

}

