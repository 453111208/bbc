<?php

class systrade_api_getOrderInfo {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取单笔子订单交易信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'oid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'子订单编号'],
            'fields'=> ['type'=>'field_list','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'获取单个子订单需要返回的字段'],
        );

        return $return;
    }

    /**
     * 获取单笔交易数据
     *
     * @param array $params 接口传入参数
     * @return array
     */
    public function getData($params)
    {
        $objMdlOrder = app::get('systrade')->model('order');
        $orderInfo = $objMdlOrder->getRow($params['fields'], array('oid'=>intval($params['oid'])) );
        return $orderInfo;
    }
}

