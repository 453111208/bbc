<?php

class systrade_api_complaints_process {

    /**
     * 接口作用说明
     */
    public $apiDescription = '平台对订单投诉同步处理结果';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getparams()
    {
        //接口传入的参数
        $return['params'] = array(
            'complaints_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'1','description'=>'投诉ID'],
            'memo'=> ['type'=>'string','valid'=>'required|min:5|max:200', 'default'=>'', 'example'=>'该投诉是因为快递运送不及时，误会导致', 'description'=>'处理备注'],
            'status'=> ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'投诉处理状态,WAIT_SYS_AGREE 等待处理,FINISHED 已完成, CLOSED 已关闭'],
        );
        return $return;
    }

    /**
     * 创建投诉订单
     */
    public function process($params)
    {
        $data['memo'] = trim($params['memo']);
        $data['status'] = trim($params['status']);
        return kernel::single('systrade_data_complaints')->process($data, $params['complaints_id']);
    }
}


