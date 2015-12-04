<?php
class ectools_api_payment_pay{
    public $apiDescription = "订单支付请求支付网关";
    public function getParams()
    {
        $return['params'] = array(
            'payment_id' => ['type'=>'string','valid'=>'required', 'description'=>'支付单编号', 'default'=>'', 'example'=>''],
            'pay_app_id' => ['type'=>'string','valid'=>'required', 'description'=>'支付方式', 'default'=>'', 'example'=>'alipay'],
            'platform' => ['type'=>'string','valid'=>'required', 'description'=>'来源平台（wap、pc）', 'default'=>'pc', 'example'=>'pc'],
            'money' => ['type'=>'string','valid'=>'required', 'description'=>'支付金额', 'default'=>'', 'example'=>'234.50'],
            'user_id' => ['type'=>'string','valid'=>'required', 'description'=>'用户id', 'default'=>'', 'example'=>'1'],
            'tids' => ['type'=>'string','valid'=>'required', 'description'=>'被支付的订单号集合,用逗号隔开', 'default'=>'', 'example'=>'1241231213432,2354234523452'],
            //'itemtype' => ['type'=>'string','valid'=>'', 'description'=>'商品类型', 'default'=>'', 'example'=>''],
        );
        return $return;
    }
    public function doPay($params)
    {
        if(!$params['platform'])
        {
            $params['platform'] = "pc";
        }

        $objMdlPayments = app::get('ectools')->model('payments');
        $objMdlPayBill = app::get('ectools')->model('trade_paybill');
        $paymentBill = $objMdlPayments->getRow('payment_id,status,money,pay_type,currency,cur_money,pay_app_id',array('payment_id'=>$params['payment_id']));
        if($paymentBill['status'] == 'succ' || $paymentBill['status'] == 'progress')
        {
            throw new Exception('该订单已经支付');
        }
        $tradePayBill = $objMdlPayBill->getList('tid',array('payment_id'=>$params['payment_id']));
        $payTids = array_bind_key($tradePayBill,'tid');

        $tids['tid'] = $params['tids'];
        $tids['fields'] = "payment,tid,status,order.title";
        $trades = app::get('ectools')->rpcCall('trade.get.list',$tids);
        $totalMoney = array_sum(array_column($trades['list'],'payment'));
        if($totalMoney != $params['money'])
        {
            throw new Exception('订单金额与需要支付金额不一致，请核对后支付');
        }
        $db = app::get('sysaftersales')->database();
        $db->beginTransaction();

        try{
            $return_url = array("topc_ctl_paycenter@finish",array('payment_id'=>$params['payment_id']));
            if($params['platform'] == "wap")
            {
                $return_url = array("topm_ctl_paycenter@finish",array('payment_id'=>$params['payment_id']));
            }
            $paymentData = array(
                'money' => $params['money'],
                'cur_money' => $params['money'],
                'pay_app_id' => $params['pay_app_id'],
                'return_url' => $return_url,
            );
            $paymentFilter['payment_id'] = $params['payment_id'];
            $result = $objMdlPayments->update($paymentData,$paymentFilter);
            if(!$result)
            {
                throw new Exception('支付失败，支付单更新失败');
            }

            foreach($trades['list'] as $val)
            {
                $data['payment'] = $val['payment'];
                $data['modified_time'] = time();
                $filter['tid'] = $val['tid'];
                $filter['payment_id'] = $params['payment_id'];
                $result = $objMdlPayBill->update($data,$filter);
                $params['item_title'][] = $val['order'][0]['title'];
                if(!$result)
                {
                    throw new Exception('支付失败，支付单明细更新失败');
                }
                if($payTids[$val['tid']])
                {
                    unset($payTids[$val['tid']]);
                }
            }

            if($payTids)
            {
                $deleteParams['tid'] = array_keys($payTids);
                $deleteParams['payment_id'] = $params['payment_id'];
                $result = $objMdlPayBill->delete($deleteParams);
                if(!$result)
                {
                    throw new Exception('支付失败，清除过期数据失败');
                }
            }

            $db->commit();
        }
        catch(Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        $paymentBill['pay_app_id'] = $params['pay_app_id'];
        $paymentBill['item_title'] = $params['item_title'][0];
        $objPayment = kernel::single('ectools_pay');
        $result = $objPayment->generate($paymentBill);
        if(!$result)
        {
            throw new Exception('支付失败,请求支付网关出错');
        }
        return true;
    }
}


