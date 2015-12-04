<?php
class topm_payment{
    /*
     *检测要支付的订单数据有效性
     *创建支付单
     *返回支付单编号
     */
    public function getPaymentId($filter)
    {
        $tids = $filter['tid'];
        if($filter['tid'] && is_array($filter['tid']))
        {
            $tids = implode(',',$filter['tid']);
        }
        $tradeParams = array(
            'user_id' => $filter['user_id'],
            'tid' => $tids,
            'fields' => 'tid,payment,user_id,status',
        );
        //获取需要支付的订单并检测其有效性
        $tradeList = app::get('topm')->rpcCall('trade.get.list',$tradeParams);
        $count = $tradeList['count'];
        $tradeList = $tradeList['list'];

        $countid = count($filter['tid']);
        if($countid != $count)
        {
            throw new \LogicException(app::get('topm')->_("支付失败，提交的订单数据有误"));
            return false;
        }

        foreach($tradeList as $key=>$value)
        {
            if($value['status'] != "WAIT_BUYER_PAY")
            {
                throw new \LogicException(app::get('topm')->_($value['tid']." 订单已被支付,请重新选择要支付订单"));
                return false;
            }
            $payment['money'] += $value['payment'];
            $payment['user_id'] = $value['user_id'];
        }
        $payment['tids'] = $tids;
        try
        {
            $paymentId = app::get('topm')->rpcCall('payment.bill.create',$payment);
        }
        catch(Exception $e)
        {
            throw $e;
        }
        return $paymentId;
    }
}
