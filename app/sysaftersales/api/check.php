<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysaftersales_api_check {

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家审核售后服务';

    public function getParams()
    {
        $return['params'] = array(
            'aftersales_bn' => ['type'=>'string','valid'=>'required', 'description'=>'申请售后的编号'],
            'shop_id' => ['type'=>'string','valid'=>'required', 'description'=>'售后单所属店铺的店铺id'],
            'check_result'  => ['type'=>'string', 'valid'=>'required', 'description'=>'审核结果,同意或不同意,(true,false)'],
            'shop_explanation'  => ['type'=>'string', 'valid'=>'', 'description'=>'商家审核处理说明'],
            //以下为申请仅退款需要传入的参数
            'total_price' => ['type'=>'money', 'valid'=>'','description'=>'退款金额'],
            'refunds_reason' => ['type'=>'string', 'valid'=>'','description'=>'退款申请原因'],
        );

        return $return;
    }

    public function check($params)
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

        $refundsData['aftersales_bn'] = $params['aftersales_bn'];
        if($params['check_result'] == 'true')
        {
            $refundsData['total_price'] = $params['total_price'];
            $refundsData['refunds_reason'] = $params['refunds_reason'];
        }
        $result = kernel::single('sysaftersales_progress')->check($filter, $params['check_result'], $params['shop_explanation'], $refundsData,$shopId);

        return $result;
    }

}

