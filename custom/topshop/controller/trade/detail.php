<?php
class topshop_ctl_trade_detail extends topshop_controller{
    public function index()
    {
        $tids = input::get('tid');
        //面包屑
        $this->runtimePath = array(
            ['url'=> url::action('topshop_ctl_index@index'),'title' => app::get('topshop')->_('首页')],
            ['url'=> url::action('topshop_ctl_trade_list@index'),'title' => app::get('topshop')->_('订单列表')],
            ['title' => app::get('topshop')->_('订单详情')],
        );

        $params['tid'] = $tids;
        $params['fields'] = "user_id,tid,status,payment,ziti_addr,post_fee,pay_type,payed_fee,receiver_state,receiver_city,receiver_district,receiver_address,trade_memo,shop_memo,receiver_name,receiver_mobile,orders.price,orders.num,orders.title,orders.item_id,orders.pic_path,total_fee,discount_fee,buyer_rate,adjust_fee,orders.total_fee,orders.adjust_fee,created_time,shop_id,need_invoice,invoice_name,invoice_type,invoice_main";
        $tradeInfo = app::get('topshop')->rpcCall('trade.get',$params,'seller');

        if(!$tradeInfo)
        {
            redirect::action('topshop_ctl_trade_list@index')->send();exit;
        }
        $userInfo = app::get('topshop')->rpcCall('user.get.account.name', ['user_id' => $tradeInfo['user_id']], 'seller');
        $tradeInfo['login_account'] = $userInfo[$tradeInfo['user_id']];

        $pagedata['trade']= $tradeInfo;
        $this->contentHeaderTitle = app::get('topshop')->_('订单详情');
        return $this->page('topshop/trade/detail.html', $pagedata);
    }

    public function ajaxGetLogi()
    {
        // 物流信息
        $tid = input::get('tid');
        $logi = app::get('topshop')->rpcCall('delivery.logistics.tracking.get',array('tid'=>$tid));
        if($logi)
        {
            $pagedata['logi'] = $logi;
        }
        return view::make('topshop/trade/trade_logistics.html',$pagedata);
    }

    public function setTradeMemo()
    {
        $params['tid'] = input::get('tid');
        $params['shop_id'] = $this->shopId;
        try
        {
            $params['shop_memo'] = input::get('shop_memo');
            $result = app::get('topshop')->rpcCall('trade.add.memo',$params);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error','',$msg,true);
        }
        $msg = app::get('topshop')->_('备注添加成功');
        $url = url::action('topshop_ctl_trade_detail@index',array('tid'=>$params['tid']));
        return $this->splash('success',$url,$msg,true);
    }
}
