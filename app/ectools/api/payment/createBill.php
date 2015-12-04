<?php
class ectools_api_payment_createBill{
    public $apiDescription = "支付单创建";
    public function getParams()
    {
        $return['params'] = array(
            'tids' => ['type'=>'string','valid'=>'required', 'description'=>'被支付订单id集合多个订单使用逗号隔开', 'default'=>'', 'example'=>''],
            'money' => ['type'=>'money','valid'=>'required', 'description'=>'支付总额', 'default'=>'', 'example'=>''],
            'user_id' => ['type'=>'string','valid'=>'required', 'description'=>'订单所属用户id', 'default'=>'', 'example'=>''],
        );
        return $return;
    }
    public function create($params)
    {
        if(!$params['tids'])
        {
            throw new \LogicException(app::get('ectools')->_("支付失败,没有要支付的订单"));
        }
        $tids = explode(',',$params['tids']);

        $trades = app::get('ectools')->rpcCall('trade.get.list',array('tid'=>$params['tids'],'fields'=>'payment,user_id,shop_id,tid,status'));
        $count = $trades['count'];
        $trades = $trades['list'];
        if(!$trades || count($tids) != $count)
        {
            throw new \LogicException(app::get('ectools')->_("支付失败,支付的订单信息有误"));
        }

        $money = array_column($trades,'payment');
        if($params['money'] != array_sum($money))
        {
            throw new \LogicException(app::get('ectools')->_("支付失败,需要支付的金额预订单金额不一致"));
        }

        $paymentId = $this->_getPaymentId($params['user_id'].$count);

        $db = app::get('sysaftersales')->database();
        $db->beginTransaction();
        try{
            $objMdlPayment = app::get('ectools')->model('payments');
            $objMdlPayBill = app::get('ectools')->model('trade_paybill');

            $payment = array(
                'payment_id' => $paymentId,
                'money' => $params['money'],
                'cur_money' => $params['money'],
                'user_id' => $params['user_id'],
                'op_id' => $params['user_id'],
                'created_time' => time(),
            );
            $result = $objMdlPayment->insert($payment);
            if(!$result)
            {
                    throw new \LogicException(app::get('ectools')->_("支付失败,支付单创建失败"));
            }

            foreach($trades as $value)
            {
                if($value['status'] != "WAIT_BUYER_PAY")
                {
                    throw new \LogicException(app::get('ectools')->_("支付失败,您有订单已经支付，请重新选择订单支付"));
                }
                $payBill = array(
                    'payment_id' => $paymentId,
                    'tid' => $value['tid'],
                    'status' =>'ready',
                    'payment' => $value['payment'],
                    'user_id' => $value['user_id'],
                    'created_time' => time(),
                    'modified_time' => time(),
                );
                $billResult = $objMdlPayBill->insert($payBill);
                if(!$billResult)
                {
                    throw new \LogicException(app::get('ectools')->_("支付失败,支付单明细添加失败"));
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


