<?php
class systrade_finder_trade{
    public $detail_basic = '基本信息';
    public $detail_items = '商品';
    public $detail_users = '会员';
    public $detail_shop = '商家';
    public $detail_other = '其他信息';

    public function __construct($app)
    {
        $this->app = $app;
        $this->app_ectools = app::get('ectools');
        $this->odr_action_buttons = array('pay','delivery','finish','refund','reship','cancel','delete');
        // 订单状态标示对应表
        $this->tradeStatus = array(
            'WAIT_BUYER_PAY' => '已下单等待付款',
            'WAIT_SELLER_SEND_GOODS' => '已付款等待发货',
            'WAIT_BUYER_CONFIRM_GOODS' => '已发货等待确认收货',
            'TRADE_FINISHED' => '已完成',
            'TRADE_CLOSED' => '已关闭',
            'TRADE_CLOSED_BY_SYSTEM' => '已关闭'
        );
        $this->shopType = array(
            'flag'=>'品牌旗舰店',
            'brand'=>'品牌专卖店',
            'cat'=>'类目专营店',
        );
    }

    public function detail_basic($tradeId)
    {
        //订单基本信息查询
        $objTrade = kernel::single('systrade_data_trade');
        $trade = $objTrade->getTradeInfo('*',array('tid'=>$tradeId));
        $trade['status_des'] = $this->tradeStatus[$trade['status']];
        $pagedata['trade'] = $trade;

        //订单支付信息查询
        $params['tids'] = $tradeId;
        $params['status'] =implode(',',array('succ','progress'));
        $params['fields'] = "*";
        $payment = app::get('systrade')->rpcCall('payment.bill.get',$params);
        $pagedata['payment'] = $payment;
        //订单发货信息查询
        $pagedata['logi'] = app::get('systrade')->rpcCall('delivery.logistics.tracking.get',array('tid'=>$tradeId));

        return view::make('systrade/admin/trade/detail.html', $pagedata)->render();
    }

    public function detail_items($tradeId)
    {
        $params = array(
            'tid' => $tradeId,
        );
        $objMdlOrder = app::get('systrade')->model('order');
        $orders = $objMdlOrder->getList("*",$params);
        $pagedata['goodsItems'] = $orders;
        return view::make('systrade/admin/trade/detail_item.html', $pagedata)->render();
    }

    public function detail_users($tradeId)
    {
        $objTrade = kernel::single('systrade_data_trade');
        $trade = $objTrade->getTradeInfo('user_id',array('tid'=>$tradeId));
        $users = kernel::single('sysuser_passport')->memInfo($trade['user_id']);
        $pagedata['user'] = $users;

        return view::make('systrade/admin/trade/detail_users.html', $pagedata)->render();
    }

    public function detail_shop($tradeId)
    {
        $objTrade = kernel::single('systrade_data_trade');
        $trade = $objTrade->getTradeInfo('shop_id',array('tid'=>$tradeId));
        $params['shop_id'] = $trade['shop_id'];
        $params['fields'] = 'shop_name,shopuser_name,open_time,email,mobile,cat.cat_name,brand.brand_name,info.company_contacts,info.company_cmobile';
        $pagedata = app::get('systrade')->rpcCall('shop.get.detail',$params);
        return view::make('systrade/admin/trade/detail_shop.html', $pagedata)->render();
    }

    public function detail_other($tradeId)
    {
        $objTrade = kernel::single('systrade_data_trade');
        if($tradeId)
        {
            $params['tid'] = $tradeId;
        }
        $rows ="shop_flag,shop_memo,trade_from,ip,trade_from,modified_time";
        $shop = $objTrade->getTradeInfo($rows,$params);
        $pagedata['shop'] = $shop;
        return view::make('systrade/admin/trade/detail_other.html', $pagedata)->render();
    }

    public $column_edit = '操作';
    public $column_edit_order = 1;
    public $column_edit_width = 60;

    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            if($row['status'] != "WAIT_BUYER_PAY")
                {
                    $colList[$k] = "";
                }

            $url = '?app=systrade&ctl=admin_trade&act=doCancel&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['tid'].'&p[1]=dead';
            $target = 'dialog::{title:\''.app::get('systrade')->_('交易取消').'\', width:300, height:200}';
            $title = app::get('systrade')->_('取消交易');
            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

        }
    }

}


