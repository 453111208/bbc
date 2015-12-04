<?php

class systrade_api_tradeabnormal_create {

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家在用户已付款未发货的情况下，申请取消异常订单';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getparams()
    {
        //接口传入的参数
        $return['params'] = array(
            'tid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单编号'],
            'reason'=> ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'取消异常订单原因'],
        );

        return $return;
    }

    /**
     * 商家创建异常订单申请
     */
    public function create($params)
    {
        return kernel::single('systrade_tradeabnormal')->create($params['tid'], $params['reason']);
    }
}

