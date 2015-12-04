<?php
class syslogistics_data_delivery{

    public function doDelivery($params)
    {
        //检测是否存在该发货单号（存在更新发货单号、不存在创建发货单号）
        //检测是否存在该物流公司（不存在返回提示信息）
        //由于和erp初定拉去的订单为一支付订单，所以收货地址暂时不会有变化,所以暂时不限制收货地址传参

        if(!$params['items'])
        {
            throw new LogicException('发货明细为空');
        }

        $objMdlDelivery = app::get('syslogistics')->model('delivery');
        $delivery = $objMdlDelivery->getRow('tid,status',array('delivery_id'=>$params['delivery_id'],'tid'=>$params['tid']));
        if($params['delivery_id'] && $delivery && $delivery['status'] == 'succ')
        {
            throw new LogicException('该发货单'.$params['delivery_id'].'中的订单'.$params['tid'].'已经发货');
        }
        else
        {
            $result = $this->create($params);
        }
        return $result;
    }

    public function create($params)
    {
        $oids = array_column($params['items']['oid']);
        $item = array_bind_key($params['items'],'oid');
        $oids = implode(',',$oids);
        $fields = "tid,receiver_name,receiver_mobile,receiver_state,receiver_district,receiver_address,need_invoice,invoice_type,invoice_name,invoice_main,post_fee,orders.tid,orders.oid,orders.sku_id,orders.bn,orders.title,orders.num";
        $filter = array(
            'tid' => $params['tid'],
            'shop_id' => $params['shop_id'],
            'seller_id' => $params['seller_id'],
            'user_id' => $params['user_id'],
            'fields' => $fields,
            'oid' => $oids,
        );
        $trades = app::get('syslogistics')->rpcCall('trade.get',$filter);
        $orders = array_bind_key($trades['orders'],'oid');
        $deliveryId = $params['delivery_id'];
        $delivery = array(
            'delivery_id' => $deliveryId,
            'tid' => $trades['tid'],
            'post_fee' =>$trades['post_fee'],
            'receiver_name' => $trades['receiver_name'],
            'receiver_state'=> $trades['receiver_state'],
            'receiver_city'=> $trades['receiver_city'],
            'receiver_district'=> $trades['receiver_district'],
            'receiver_address'=> $trades['receiver_address'],
            'receiver_zip'=> $trades['receiver_zip'],
            'receiver_mobile'=> $trades['receiver_mobile'],
            'receiver_phone'=> $trades['receiver_phone'],
            'shop_id' =>$params['shop_id'],
            'user_id' =>$params['user_id'],
            'seller_id' =>$params['seller_id'],
            'logi_id' => $params['corp_id'],
            'logi_name' => $params['corp_name'],
            'corp_code' => $params['corp_code'],
            'logi_no' => trim($params['logi_no']),
            'dlytmpl_id' => $params['template_id'],
            'memo' => $params['memo'],
            'is_protect' => 0,
            'status' => "succ",
            't_send' => time(),
            't_confirm' => time(),
            't_begin' => time(),
        );

        $objMdlDelivery = app::get('syslogistics')->model('delivery');
        $objMdlDeliveryDetail = app::get('syslogistics')->model('delivery_detail');
        $db = app::get('syslogistics')->database();
        $db->beginTransaction();
        try{
            $result = $objMdlDelivery->save($delivery);
            if(!$result)
            {
                throw new \LogicException('发货单创建失败');
            }

            foreach($item as $order)
            {
                if($orders[$order['oid']])
                {
                    $order['delivery_id'] = $result;
                    $order['item_type'] = "item";
                    $isSave = $objMdlDeliveryDetail->save($order);
                    if(!$isSave)
                    {
                        throw new \LogicException("发货单明细保存失败");
                    }
                }
                else
                {
                    throw new \LogicException("被发货的子订单不存在");
                }
            }

            $filter = array(
                'tid'=>$params['tid'],
                'items' => json_encode($item),
                'shop_id' => $params['shop_id'],
            );
            app::get('syslogistics')->rpcCall('trade.update.delivery.status',$filter);
            $db->commit();
        }
        catch(Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return $result;
    }

    private function _getDeliveryId($tid)
    {
        $sign = '1'.date("Ymd");
        while(true)
        {
            $microtime = utils::microtime();
            mt_srand($microtime);
            $randval = substr(mt_rand(), 0, -3) . rand(100, 999);

            $db = app::get('sysitem')->database();
            $aRet = $db->executeQuery('select count(*) as c from syslogistics_delivery where tid="'.$tid.'" and delivery_id="'.$sign.$randval.'"')->fetchAll();
            if( !$aRet['c'] )
                break;
        }
        return $sign.$randval;
    }
}
