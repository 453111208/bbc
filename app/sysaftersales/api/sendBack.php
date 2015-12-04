<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 消费者提交退货物流信息
 */
class sysaftersales_api_sendback {

    /**
     * 接口作用说明
     */
    public $apiDescription = '消费者回寄退货物流信息';

    /**
     * 消费者提交退货物流信息参数
     */
    public function getParams()
    {
        /*
         * 参数说明：corp_code 必填，但是如果值为other 则会判断是否有logi_name
         */
        $return['params'] = array(
            'aftersales_bn' => ['type'=>'string','valid'=>'required', 'description'=>'申请售后的订单编号'],
            'user_id' => ['type'=>'string','valid'=>'', 'description'=>'售后单所属店用户id'],
            'corp_code' => ['type'=>'int', 'valid'=>'required', 'description'=>'物流公司代码'],
            'logi_name' => ['type'=>'string', 'valid'=>'', 'description'=>'物流公司名称'],
            'logi_no' => ['type'=>'string', 'valid'=>'required', 'description'=>'物流单号'],
            'receiver_address' => ['type'=>'string', 'valid'=>'', 'description'=>'换货发货地址'],
            'mobile' => ['type'=>'string', 'valid'=>'', 'description'=>'手机号'],
        );

        return $return;
    }

    /**
     * 消费者提交退货物流信息
     */
    public function send($params)
    {
        if($params['oauth']['auth_type'] == "member")
        {
            $userId = $params['oauth']['account_id'];
            unset($params['oauth']);
        }
        else
        {
            throw new \LogicException('登录信息有误');
        }

        $filter['aftersales_bn'] = $params['aftersales_bn'];

        if($params['user_id'])
        {
            $filter['user_id'] = $params['user_id'];
        }

        $data['corp_code'] = $params['corp_code'];
        $data['logi_name'] = $params['logi_name'];
        $data['logi_no'] = $params['logi_no'];
        $data['receiver_address'] = $params['receiver_address'];
        $data['mobile'] = $params['mobile'];
        $result = kernel::single('sysaftersales_progress')->sendGoods($filter,'buyer_back',$data,$userId);
        return $result;
    }
}
