<?php
class ectools_api_paymentMoney{

    public $apiDescription = '更新支付单金额';
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'payment_id' => ['type'=>'string', 'valid'=>'required','description'=>'支付单号', 'default'=>'', 'example'=>''],
            'money' => ['type'=>'string', 'valid'=>'required','description'=>'支付单总金额', 'default'=>'', 'example'=>''],
            'cur_money' => ['type'=>'string', 'valid'=>'required','description'=>'支付单货币费率之后的金额', 'default'=>'', 'example'=>''],
            'trade_own_money' => ['type'=>'json', 'valid'=>'required','description'=>'支付单中包含的订单及金额', 'default'=>'', 'example'=>''],
        );
        return $return;
    }


    public function update($params)
    {
        $db = app::get('sysaftersales')->database();
        $db->beginTransaction();

        try{
            $data['money'] = $params['money'];
            $data['cur_money'] = $params['cur_money'];
            $filter['payment_id'] = $params['payment_id'];
            $objMdlPayment = app::get('ectools')->model('payments');
            $objMdlPayBill = app::get('ectools')->model('trade_paybill');
            $result = $objMdlPayment->update($data,$filter);
            if(!$result)
            {
                throw new Exception('更新支付单失败');
            }

            $trade_own_money = json_decode($params['trade_own_money'],true);
            foreach($trade_own_money as $key=>$val)
            {
                $billdata['payment'] = $val;
                $billdata['modified_time'] = time();
                $billfilter['tid'] = $key;
                $billfilter['payment_id'] = $params['payment_id'];
                $result = $objMdlPayBill->update($billdata,$billfilter);
                if(!$result)
                {
                    throw new Exception('更新支付单失败');
                }
            }
            $db->commit();
        }
        catch(Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }
}

