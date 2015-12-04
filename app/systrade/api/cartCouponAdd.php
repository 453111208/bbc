<?php

class systrade_api_cartCouponAdd {

    /**
     * 接口作用说明
     * trade.cart.cartCouponAdd
     */
    public $apiDescription = '选择的优惠券放入购物车优惠券表';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'coupon_code' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'优惠券编码'],
            // 'coupon_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'优惠券id'],
            'shop_id' => ['type'=>'int', 'valid'=>'required|integer', 'default'=>'', 'example'=>'','description'=>'店铺id'],
            'user_id' => ['type'=>'int', 'valid'=>'required|integer', 'default'=>'', 'example'=>'','description'=>'用户id'],
        );

        return $return;
    }

    /**
     * 选择的优惠券放入购物车优惠券表
     *
     * @param array $params 接口传入参数
     * @return array
     */
    public function cartCouponAdd($params)
    {
        $userId = $params['user_id'];
        
        $objLibCart = kernel::single("systrade_data_cart", $userId);
        return  $objLibCart->addCouponCart($params['coupon_code'], $params['shop_id']);
    }
}

