<?php

class systrade_api_getUserBuyerList {

    /**
     * 接口作用说明
     */
    public $apiDescription = '根据用户ID，查询用户最近购买记录';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户id'],
        );

        return $return;
    }

    public function get($params)
    {
        $objMdlOrder = app::get('systrade')->model('order');
        $filter['user_id'] = $params['user_id'];

        $fields = 'user_id,item_id,price,title,pic_path';
        $buyerItemList = $objMdlOrder->getList($fields, $filter,0,5, 'pay_time desc');
        return $buyerItemList;
    }
}
