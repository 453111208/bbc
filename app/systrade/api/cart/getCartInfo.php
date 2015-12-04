<?php

class systrade_api_cart_getCartInfo {

    /**
     * 接口作用说明
     * trade.cart.getCartInfo
     */
    public $apiDescription = '获取购物车信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'mode' => ['type'=>'string', 'valid'=>'', 'default'=>'cart', 'example'=>'fastbuy', 'description'=>'购物车类型(立即购买，购物车)'],
            'needInvalid' => ['type'=>'boolean', 'valid'=>'', 'default'=>'true', 'example'=>'true', 'description'=>'是否需要显示失效商品'],
            'platform' => ['type'=>'string', 'valid'=>'', 'default'=>'pc', 'example'=>'true', 'description'=>'平台'],
            'user_id' => ['type'=>'int', 'valid'=>'required|integer', 'default'=>'', 'example'=>'true', 'description'=>'用户id'],
        );

        return $return;
    }

    /**
     * 获取购物车信息
     *
     * @param array $params 接口传入参数
     * @return array
     */
    public function getCartInfo($params)
    {
        $userId = $params['user_id'];
        
        $cartFilter['mode'] = $params['mode'] ? $params['mode'] :'cart';
        $cartFilter['user_id'] = $params['oauth']['account_id']; //必须
        $platform = in_array($params['platform'], array('pc', 'wap')) ? $params['platform'] : 'pc';
        if( !isset($params['needInvalid']) )
        {
            $params['needInvalid'] = true;
        }
        $needInvalid = $params['needInvalid'] ? true : false;
        $objLibCart = kernel::single('systrade_data_cart', $userId);
        return  $objLibCart->getCartInfo($cartFilter, $needInvalid, $platform);
    }
}

