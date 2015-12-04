<?php
class sysuser_api_getCoupon {

    /**
     * 接口作用说明
     */
    public $apiDescription = '领取优惠券';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填'],
            'coupon_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'优惠券ID必填'],
        );

        return $return;
    }

    public function getCoupon($apiData)
    {
        $coupon_id = $apiData['coupon_id'];
        $user_id = $apiData['user_id'];
        return kernel::single('sysuser_data_coupon')->getCouponCode($coupon_id, $user_id);
    }
}
