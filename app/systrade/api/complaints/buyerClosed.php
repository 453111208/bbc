<?php

class systrade_api_complaints_buyerClose {

    /**
     * 接口作用说明
     */
    public $apiDescription = '买家撤销订单投诉';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getparams()
    {
        //接口传入的参数
        $return['params'] = array(
            'complaints_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'1','description'=>'订单投诉ID'],
            'buyer_close_reasons'=> ['type'=>'string','valid'=>'required|min:5|max:200', 'default'=>'', 'example'=>'', 'description'=>'买家撤销订单投诉原因'],
        );

        return $return;
    }

    /**
     * 获取单个投诉订单详情
     */
    public function close($params)
    {
        if($params['oauth']['auth_type'] == "member" && $params['oauth']['account_id'])
        {
            $userId = $params['oauth']['account_id'];
            unset($params['oauth']);
        }
        else
        {
            throw new \LogicException('登录已过期，请重新登录');
        }

        $complaintsId = $params['complaints_id'];
        $buyerCloseReasons = $params['buyer_close_reasons'];
        return kernel::single('systrade_data_complaints')->buyerClose($complaintsId, $userId , $buyerCloseReasons);
    }
}

