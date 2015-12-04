<?php
class topshop_ctl_trade_flow extends topshop_controller{

    /**
     * 产生订单发货页面
     * @params string order id
     * @return string html
     */
    public function godelivery()
    {
        $tid = input::get('tid');
        if(!$tid)
        {
            header('Content-Type:application/json; charset=utf-8');
            echo '{error:"'.app::get('topshop')->_("订单号传递出错.").'",_:null}';exit;
        }
        $params['tid'] = $tid;
        $params['fields'] = "tid,receiver_name,receiver_mobile,receiver_state,receiver_district,receiver_address,need_invoice,ziti_addr,invoice_type,invoice_name,invoice_main,orders.tid,orders.oid,dlytmpl_id";
        $tradeInfo = app::get('topshop')->rpcCall('trade.get',$params,'seller');

        $oids = implode(',',array_column($tradeInfo['orders'],'oid'));
        $delivery = $this->createDelivery(array('tid'=>$tid,'oids'=>$oids));
        $pagedata['delivery'] = $delivery;
        $pagedata['tradeInfo'] = $tradeInfo;

        //获取用户的物流模板
        if($tradeInfo['dlytmpl_id'] == 0 && $tradeInfo['ziti_addr'])
        {
            $dlycorp = app::get('topshop')->rpcCall('logistics.dlycorp.get.list');
            $pagedata['dlycorp'] = $dlycorp['data'];
        }
        else
        {
            $dtytmpl = app::get('topshop')->rpcCall('logistics.dlytmpl.get',array('shop_id'=>$this->shopId,'template_id'=>$tradeInfo['dlytmpl_id']));
            $pagedata['dtyList'] = $dtytmpl;
        }
        return view::make('topshop/trade/godelivery.html', $pagedata);
    }

    public function createDelivery($postdata=null)
    {
        if(!$postdata)
        {
            $postdata = input::get('trade');
        }
        $postdata['seller_id'] = $this->sellerId;
        $postdata['shop_id'] = $this->shopId;
        $postdata['op_name'] = $this->sellerName;
        $deliveryId = app::get('topshop')->rpcCall('delivery.create',$postdata);
        $pagedata['delivery_id'] = $deliveryId;
        $pagedata['tid'] = $postdata['tid'];
        return $pagedata;
    }

    /**
     * 发货订单处理
     * @params null
     * @return null
     */
    public function dodelivery()
    {
        $sdf = input::get();
        if(empty($sdf['logi_no']))
        {
            return $this->splash('error',null, '发货单号不能为空', true);
        }

        $sdf['logi_no'] = trim($sdf['logi_no']);
        $sdf['seller_id'] = $this->sellerId;
        $sdf['shop_id'] = $this->shopId;
        try
        {
            app::get('topshop')->rpcCall('trade.delivery',$sdf,'seller');
        }
        catch (Exception $e)
        {
            return $this->splash('error',null, $e->getMessage(), true);
        }
        return $this->splash('success',null, '发货成功', true);
    }
}


