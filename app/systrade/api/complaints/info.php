<?php

class systrade_api_complaints_info {

    /**
     * 接口作用说明
     */
    public $apiDescription = '根据自订单号获取单个订单投诉详情';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getparams()
    {
        //接口传入的参数
        $return['params'] = array(
            'oid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'1507021608160001','description'=>'子订单ID'],
            'fields'=> ['type'=>'field_list','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'获取单个订单投诉的字段'],
        );

        //如果参数fields中存在orders，则表示需要获取子订单的数据结构
        $return['extendsFields'] = ['orders'];
        return $return;
    }

    /**
     * 获取单个投诉订单详情
     */
    public function get($params)
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
        return kernel::single('systrade_data_complaints')->getInfo($params['oid'], $userId ,$params['fields']['rows'], $params['fields']['extends']['orders']);
    }
}
