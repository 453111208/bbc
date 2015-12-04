<?php
class syslogistics_api_tradeDeliveryForopen{
    public $apiDescription = "商户线下发货接口(oauth for erp, 一次性全部商品发货)";
    public function getParams()
    {
        $return['params'] = array(
            'delivery_id' =>['type'=>'string','valid'=>'', 'description'=>'发货单流水编号','default'=>'','example'=>'1'],
            'tid' =>['type'=>'string','valid'=>'required', 'description'=>'订单号','default'=>'','example'=>'1'],

            'corp_code' =>['type'=>'string','valid'=>'required', 'description'=>'物流公司编码','default'=>'','example'=>'1'],
            'corp_no' =>['type'=>'string','valid'=>'required', 'description'=>'运单号','default'=>'','example'=>'1'],

            'items' =>['type'=>'json','valid'=>'required', 'description'=>'发货单明细,json格式(num,bn)','default'=>'','example'=>'1'],
            'memo' =>['type'=>'string','valid'=>'', 'description'=>'备注','default'=>'','example'=>'1'],
        );
        return $return;
    }

    public function tradeDelivery($params, $oauth)
    {
        //获取登陆用户的id以及店铺号
        $delivery['seller_id'] = $oauth['sellerId'];
        $delivery['shop_id'] = $oauth['shop_id'];

        //将请求的商品信息json格式处理为需要使用的数据结构
        $tradeInfo = array();
        $delivery['tid'] = $params['tid'];
        $delivery['items'] = $this->__getItems($params['tid'], $result['shop_id'], $params['items'], $tradeInfo);
        $delivery['delivery_id'] = $params['delivery_id'];
        $delivery['logi_no'] = $params['corp_no'];
        $delivery['post_fee'] = $tradeInfo['post_fee'];
        $delivery['user_id'] = $tradeInfo['user_id'];
        $delivery['memo'] = $params['memo'];


        $objMdlDlytmpl = app::get('syslogistics')->model('dlytmpl');
        $objMdlDlyCorp = app::get('syslogistics')->model('dlycorp');
        $tmpl = $objMdlDlytmpl->getRow('corp_id,template_id',array('template_id' => $tradeInfo['dlytmpl_id']));
        if($tmpl)
        {
            $corp = $objMdlDlyCorp->getRow('corp_name,corp_code,corp_id',array('corp_id'=>$tmpl['corp_id']));
        }
        if(!$corp)
        {
            throw new LogicException('快递公司不存在！');
        }
        if(strtoupper($params['corp_code']) != strtoupper($corp['corp_code']))
        {
            throw new LogicException('快递公司不匹配！发货失败');
        }

        $delivery['corp_id'] = $corp['corp_id'];
        $delivery['template_id'] = $tmpl['template_id'];
        $delivery['corp_name'] = $corp['corp_name'];
        $delivery['corp_code'] = $corp['corp_code'];

//      print_r($delivery);exit;
        $objLogisticsDelivery = kernel::single('syslogistics_data_delivery');
        $result = $objLogisticsDelivery->doDelivery($delivery);

        return true;
    }

    private function __getItems($tradeId, $shopId, $items, &$tradeInfo)
    {
        //将数据从json格式转化为array
        $items = json_decode($items,1);
        $fields = 'tid,shop_id,user_id,status,post_fee,dlytmpl_id,orders.bn,'
            . 'orders.num,orders.oid,orders.title,orders.sku_id';

        //获取订单的订单详情，并且返回这个订单数据
        $requestParams = ['tid' => $tradeId, 'fields'=>$fields];
        $tradeInfo = app::get('syslogistics')->rpcCall('trade.get', $requestParams);

        $ordersInfo = $tradeInfo['orders'];
        $fmt_ordersInfo = array();
        //
        foreach($ordersInfo as $orderInfo)
        {
            $skuBn = $orderInfo['bn'];
            $fmt_ordersInfo[$skuBn] = $orderInfo;
        }

        //这里是对item的数据的验证
        $deliveryDetailValidate = array(
            'num' =>'required|numeric',
            'bn' =>'required',
        );

        $newItems = [];
        foreach($items as $item)
        {
            //验证每个item的数据格式的正确性
            $it = [];
            $validator = validator::make($item, $deliveryDetailValidate);
            if( $validator->fails() )
            {
                $errors = json_decode( $validator->messages(), 1 );
                foreach( $errors as $error )
                {
                    throw new LogicException( $error[0] );
                }
            }

            //TODO order的数据格式
            $bn = $item['bn'];

            if($fmt_ordersInfo[$bn] == null)
                throw new LogicException( "该订单中，货品不存在。bn:".$bn );


            $it['bn'] = $bn;
            $it['oid'] = $fmt_ordersInfo[$bn]['oid'];
            $it['num'] = $item['num'];
            $it['title'] = $fmt_ordersInfo[$bn]['title'];
            $it['sku_id'] = $fmt_ordersInfo[$bn]['sku_id'];

            $newItems[] = $it;
        }

        return $newItems;
    }

    private function __safeCheck($tradeInfo, $shopId)
    {
        if( $tradeInfo == null )
            throw new LogicException('进行发货操作的订单不存在，请确认订单号');

        if( $tradeInfo['shop_id'] != $shopId )
            throw new LogicException('该商品不属于当前登陆账户的店铺，请确认订单号');

        if( $tradeInfo['status'] != 'WAIT_SELLER_SEND_GOODS' )
            throw new LogicException('只能对待发货订单进行发货操作，请确认该订单的发货状态');

        return null;
    }

}

