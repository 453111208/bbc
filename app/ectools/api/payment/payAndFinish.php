<?php
class ectools_api_payment_payAndFinish{
    public $apiDescription = "线下支付单创建并支付完成";
    public function getParams()
    {
        $data['params'] = array(
            'tids' => ['type'=>'string','valid'=>'required', 'description'=>'被支付订单id集合多个订单使用逗号隔开', 'default'=>'', 'example'=>''],
            'pay_app_id' => ['type'=>'string','valid'=>'required', 'description'=>'支付方式', 'default'=>'', 'example'=>'alipay'],
            'money' => ['type'=>'string','valid'=>'required', 'description'=>'支付金额', 'default'=>'', 'example'=>'234.50'],
            'user_id' => ['type'=>'int','valid'=>'required', 'description'=>'用户id', 'default'=>'', 'example'=>'1'],
            'seller_id' => ['type'=>'int','valid'=>'required', 'description'=>'订单所属商家', 'default'=>'', 'example'=>'1'],
            'memo' => ['type'=>'int','valid'=>'required', 'description'=>'操作备注', 'default'=>'', 'example'=>'1'],
        );
        return $data;
    }
    public function payAndFinish($params)
    {
        $paymentId = $this->_getPaymentId($params['user_id'].$count);
        $objMdlPayments = app::get('ectools')->model('payments');
        $objMdlPayBill = app::get('ectools')->model('trade_paybill');

        $tids['tid'] = $params['tids'];
        $tids['fields'] = "payment,tid,status";
        $trades = app::get('ectools')->rpcCall('trade.get.list',$tids);
        $totalMoney = array_sum(array_column($trades['list'],'payment'));
        if($totalMoney != $params['money'])
        {
            throw new Exception('订单金额与需要支付金额不一致，请核对后支付');
        }
        $db = app::get('sysaftersales')->database();
        $db->beginTransaction();

        try{
            $paymentData = array(
                'payment_id' => $paymentId,
                'money' => $params['money'],
                'cur_money' => $params['money'],
                'pay_app_id' => $params['pay_app_id'],
                'status' => 'succ',
                'user_id' => $params['user_id'],
                'pay_type' => 'offline',
                'pay_name' => '线下支付',
                'payed_time' => time(),
                'created_time' => time(),
                'modified_time' => time(),
                'op_id' => $params['seller_id'],
                'memo' => $params['memo'],
            );
            $result = $objMdlPayments->save($paymentData);
            if(!$result)
            {
                throw new Exception('支付失败，支付单更新失败');
            }

            foreach($trades['list'] as $val)
            {
                $data['payment_id'] = $paymentId;
                $data['tid'] = $val['tid'];
                $data['payment'] = $val['payment'];
                $data['user_id'] = $params['user_id'];
                $data['status'] = "succ";
                $data['created_time'] = time();
                $data['modified_time'] = time();
                $data['payed_time'] = time();
                $result = $objMdlPayBill->save($data,$filter);
                if(!$result)
                {
                    throw new Exception('支付失败，支付单明细更新失败');
                }
            }
            $db->commit();
        }
        catch(Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return $paymentId;
    }

    private function _getPaymentId($tradeId)
    {
        $objMdlPayment = app::get('ectools')->model('payments');
        $tradeId = str_pad($tradeId,5,time(),STR_PAD_LEFT);
        $i = rand(0,99999);
        do{
            if(99999==$i){
                $i=0;
            }
            $i++;
            $paymentId = date('ymdHi').str_pad($i,5,'0',STR_PAD_LEFT).$tradeId;
            $row = $objMdlPayment->getRow('payment_id',array('payment_id'=>$paymentId));
        }while($row);
        return $paymentId;
    }
}


