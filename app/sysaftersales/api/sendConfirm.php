<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 消费者提交退货物流信息
 */
class sysaftersales_api_sendConfirm {

    /**
     * 接口作用说明
     */
    public $apiDescription = '消费者申请换货，商家确认收到回寄商品，进行重新进行发货';

    /**
     * 消费者提交退货物流信息参数
     */
    public function getParams()
    {
        $return['params'] = array(
            'aftersales_bn' => ['type'=>'string','valid'=>'required', 'description'=>'申请售后的订单编号'],
            'shop_id' => ['type'=>'string','valid'=>'', 'description'=>'售后单所有书店铺的店铺id'],
            'corp_code' => ['type'=>'int', 'valid'=>'', 'description'=>'物流公司代码'],
            'logi_name' => ['type'=>'string', 'valid'=>'', 'description'=>'物流公司名称'],
            'logi_no' => ['type'=>'string', 'valid'=>'', 'description'=>'物流单号'],
        );

        return $return;
    }

    /**
     * 消费者提交退货物流信息
     */
    public function send($params)
    {
        if($params['oauth']['auth_type'] == "shop")
        {
            $shopId = app::get('sysaftersales')->rpcCall('shop.get.loginId',array('seller_id'=>$params['oauth']['account_id']),'seller');
            unset($params['oauth']);
        }
        else
        {
            throw new \LogicException('登录信息有误');
        }

        $filter['aftersales_bn'] = $params['aftersales_bn'];
        $filter['shop_id'] = $params['shop_id'];

        $data['corp_code'] = $params['corp_code'];
        $data['logi_name'] = $params['logi_name'];
        $data['logi_no'] = $params['logi_no'];
        $result = kernel::single('sysaftersales_progress')->sendGoods($filter, 'seller', $data,$shopId);
        return $result;
    }
}

