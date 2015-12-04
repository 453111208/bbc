<?php
class systrade_api_trade_payFinish{
    public $apiDescription = "订单支付状态改变";

    public function getParams()
    {
        $return['params'] = array(
            'tid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单id'],
            'payment' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'已支付金额'],
        );
        return $return;
    }

    public function tradePay($params)
    {
        $tid = $params['tid'];
        $objTrade = kernel::single('systrade_data_trade');
        $tradeInfo = $objTrade->getTradeInfo('payment,status,tid',['tid'=>$tid]);
        if($tradeInfo['status'] != 'WAIT_BUYER_PAY' )
        {
            return true;
        }

        $objMdlOrder = app::get('systrade')->model('order');
        try{
            foreach($tradeInfo['order'] as $orderkey=>$orderval)
            {
                $updateStore = array(
                    'item_id' => $orderval['item_id'],
                    'sku_id' => $orderval['sku_id'],
                    'quantity' => $orderval['num'],
                    'sub_stock' => 0,
                    'status' => 'success',
                );
                app::get('systrade')->rpcCall('item.store.minus',$updateStore) ;
            }

            $tradeData['data']['status']='WAIT_SELLER_SEND_GOODS';
            $tradeData['data']['modified_time']=time();
            $tradeData['data']['pay_time']=time();
            $tradeData['data']['payed_fee'] = $params['payment'];
            $tradeData['filter']['tid'] = $tid;
            $result = $objTrade->updateTrade($tradeData);
            if(!$result)
            {
                throw new \LogicException("主订单支付状态更新失败");
            }
            $orders = array(
                'status'=>'WAIT_SELLER_SEND_GOODS',
                'pay_time'=>$nowTime
            );
            if(!$objMdlOrder->update($orders, array('tid'=>$tid) ) )
            {
                $msg = "子订单支付状态修改失败";
                throw new \LogicException($msg);
            }
            $this->addLog($tid,$params);
        }
        catch(\Exception $e)
        {
            throw $e;
        }
        return true;
    }

    /**
     * 记录订单取消日志
     * @param int &$canCancelTid 订单数据[操作者信息]
     * @param array &$params       成功标识
     */
    private function addLog($tid )
    {
        $objLibLog = kernel::single('systrade_data_trade_log');
        $logText = '订单付款成功！';
        $sdfTradeLog = array(
            'rel_id'   => $tid,
            'op_id'    => 0,
            'op_name'  => '系统',
            'op_role'  => 'system',
            'behavior' => 'payment',
            'log_text' => $logText,
        );
        if(!$objLibLog->addLog($sdfTradeLog))
        {
            $msg = "log记录失败";
            throw new \LogicException($msg);
            return false;
        }
        return true;
    }
}


