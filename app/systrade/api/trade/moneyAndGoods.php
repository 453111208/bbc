<?php
class systrade_api_trade_moneyAndGoods{
    public $apiDescription = "确认收款、收货";
    public function getParams()
    {
        $data['params'] = array(
            'tid' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单编号'],
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单所属店铺id'],
            'seller_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单所属店铺的操作员'],
            'memo' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'操作备注'],
        );
        return $data;
    }
    public function receipt($params)
    {
        //获取被操作的订单信息
        $params['fields'] = "user_id,tid,shop_id,status,payment,post_fee,pay_type,receiver_state,receiver_city,receiver_district,receiver_address,receiver_name,receiver_mobile,orders.tid,orders.oid,orders.item_id,orders.sku_id,orders.num";
        $tradeInfo = app::get('topshop')->rpcCall('trade.get',$params);
        try{
            $paymentParams = array(
                'tids' => $tradeInfo['tid'],
                'money' => $tradeInfo['payment'],
                'user_id' => $tradeInfo['user_id'],
                'seller_id' => $params['seller_id'],
                'memo' => $params['memo'],
                'pay_app_id' => "offline",
            );
            $paymentId = app::get('topc')->rpcCall('payment.trade.payandfinish',$paymentParams);

            //创建线下支付单,并完成支付单
            if(!$paymentId)
            {
                throw new Exception('操作失败，支付失败');
            }

            foreach($tradeInfo['orders'] as $order)
            {
                $updateStore = array(
                    'item_id' => $order['item_id'],
                    'sku_id' => $order['sku_id'],
                    'quantity' => $order['num'],
                    'sub_stock' => 0,
                    'status' => 'success',
                );
                app::get('systrade')->rpcCall('item.store.minus',$updateStore) ;
            }

            //更新订单支付状态，并完成订单
            $objTrade = kernel::single('systrade_data_trade');
            $tradeData['data']['status']='TRADE_FINISHED';
            $tradeData['data']['modified_time']=time();
            $tradeData['data']['pay_time'] = time();
            $tradeData['data']['end_time'] = time();
            $tradeData['data']['payed_fee'] = $tradeInfo['payment'];
            $tradeData['filter']['tid'] = $params['tid'];
            $result = $objTrade->updateTrade($tradeData);

            if(!$result)
            {
                throw new \LogicException("主订单支付状态更新失败");
            }
            $orders = array(
                'status'=>'TRADE_FINISHED',
                'pay_time'=>time(),
                'end_time'=>time(),
            );
            $objMdlOrder = app::get('systrade')->model('order');
            if(!$objMdlOrder->update($orders, array('tid'=>$params['tid']) ) )
            {
                $msg = "子订单支付状态修改失败";
                throw new \LogicException($msg);
            }
            $this->addLog($params['tid']);
        }
        catch(Exception $e){
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


