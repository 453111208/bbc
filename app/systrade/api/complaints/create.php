<?php

class systrade_api_complaints_create {

    /**
     * 接口作用说明
     */
    public $apiDescription = '买家对不满意订单发起投诉';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getparams()
    {
        //接口传入的参数
        $return['params'] = array(
            'oid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'1506181717250001','description'=>'投诉子订单号'],
            'tel'=> ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'13918987654', 'description'=>'投诉人联系方式'],
            'image_url'=> ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'投诉图片凭证URL，最多5张图片，URL用逗号隔开'],
            'complaints_type'=> ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'售后问题', 'description'=>'投诉类型'],
            'content'=> ['type'=>'string','valid'=>'required|min:5|max:300', 'default'=>'', 'example'=>'商家描述可以随时退款，现在说不能退', 'description'=>'投诉问题的具体描述'],
        );

        return $return;
    }

    /**
     * 创建投诉订单
     */
    public function create($params)
    {
        if($params['oauth']['auth_type'] == "member")
        {
            $userId = $params['oauth']['account_id'];
            unset($params['oauth']);
        }
        else
        {
            throw new \LogicException('登录超时，请重新登录');
        }

        return kernel::single('systrade_data_complaints')->create($params, $userId);
    }
}


