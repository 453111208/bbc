<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 * 本类提供商品详情页显示数据组织
 */
class systrade_payment_gather{

    public function checkTradeData($paymentId)
    {
        $objTrade = kernel::single('systrade_data_trade');
        $params = array('payment_id'=>$paymentId);
        $params['fields'] = "payment_id,status,tids,money,pay_type,currency,cur_money,pay_app_id";
        $payment = app::get('systrade')->rpcCall('payment.bill.get',$params);
        //检测支付单状态
        if($payment['status'] == 'succ' || $payment['status'] == "progress")
        {
            throw new \LogicException(app::get('systrade')->_("该订单已经支付"));
            return false;
        }

        //检测支付单中的订单和订单中信息是否一致
        $tids['tid'] = explode(',',$payment['tids']);
        $tids['status'] = 'WAIT_BUYER_PAY';
        $countTid = $objTrade->countTrade($tids);
        if(count($tids['tid']) !== $countTid)
        {
            throw new \LogicException(app::get('systrade')->_("支付失败，提交的订单数据有误"));
            return false;
        }
        return $payment;
    }

    public function getTradeList($filter)
    {
        if($filter['tid'])
        {
            $params['filter']['tid'] = $filter['tid'];
        }

        $objTrade = kernel::single('systrade_data_trade');
        $trades = $objTrade->getTradeList($params);
        if(count($trades) == count($filter['tid']))
        {
            foreach($trades as $key=>$value)
            {
                $returns['tid'][$key] = $value['tid'];
                $returns['total_money'] += $value['payment'];
            }
            return $returns;
        }
        else
        {
            throw new \LogicException(app::get('systrade')->_("支付失败，提交的订单数据有误"));
            return false;
        }
    }

    public function doPay($filter)
    {
        if(!$filter['pay_app_id'])
        {
            throw new \LogicException(app::get('systrade')->_("请选择支付方式!"));
            return false;
        }
        //检测该支付单是否被支付
        //检测该支付单中的订单是否有异常
        $payments = $this->checkTradeData($filter['payment_id']);
        if(!$payments)
        {
            throw new \LogicException(app::get('systrade')->_("支付失败"));
            return false;
        }
        $payments['pay_app_id'] = $filter['pay_app_id'];
        $payments['return_url'] = $filter['return_url'];
        //更新支付单的支付方式
        //请求第三方支付接口
        $updateResult = payment::update($payments);
        if(!$updateResult)
        {
            throw new \LogicException(app::get('systrade')->_("支付信息更新失败"));
            return false;
        }

        $objPayment = kernel::single('ectools_pay');
        $result = $objPayment->generate($payments);
        if(!$result)
        {
            throw new \LogicException(app::get('systrade')->_("支付失败"));
            return false;
        }
        return true;
    }
}

