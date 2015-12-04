<?php

class systrade_api_cartCouponCancel {

    /**
     * 接口作用说明
     */
    public $apiDescription = '取消优惠券';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     * trade.cart.cartCouponCancel
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'coupon_code' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'优惠券编码'],
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'优惠券编码'],
        );

        return $return;
    }

    /**
     * 取消优惠券
     *
     * @param array $params 接口传入参数
     * @return array
     */
    public function cartCouponCancel($params)
    {
        $userId = $params['oauth']['account_id'];
        
        $objLibCart = kernel::single('systrade_data_cart', $userId);
        return $objLibCart->cancelCouponCart($params['coupon_code'], $params['shop_id']);
    }
}

