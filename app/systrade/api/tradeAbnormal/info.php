<?php

class systrade_api_tradeAbnormal_info {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取单条异常订单的详情';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getparams()
    {
        //接口传入的参数
        $return['params'] = array(
            'id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'申请取消异常订单ID'],
        );

        return $return;
    }

    /**
     * 获取单条异常订单的详情
     */
    public function getData($params)
    {
        return kernel::single('systrade_tradeabnormal')->getInfo($params['id']);
    }
}

