<?php
class topm_ctl_member_trade extends topm_ctl_member{

    public $tradeStatus = array(
        '1' => 'WAIT_BUYER_PAY',
        '2' => 'WAIT_SELLER_SEND_GOODS',
        '3' => 'WAIT_BUYER_CONFIRM_GOODS',
    );

    /**
     * @brief 会员中心订单列表
     *
     * @return
     */
    public function index()
    {
        $filter = input::get();
        $pagedata = $this->__getTrade($filter);
        if(empty($pagedata['trades']))
        {
            return $this->page('topm/member/trade/emptylist.html', $pagedata);
        }
        return $this->page('topm/member/trade/list.html', $pagedata);
    }

    public function tradeList()
    {
        $filter = input::get();
        $pagedata = $this->__getTrade($filter);
        return $this->page('topm/member/trade/list.html', $pagedata);
    }

    /**
     * @brief 订单详情
     *
     * @return
     */
    public function detail()
    {
        $this->setLayoutFlag('order_detail');
        $params['tid'] = input::get('tid');
        $params['user_id'] = userAuth::id();
        $params['fields'] = "tid,status,payment,post_fee,receiver_state,receiver_city,receiver_district,ziti_addr,receiver_address,trade_memo,receiver_name,receiver_mobile,orders.oid,orders.price,orders.num,orders.title,orders.aftersales_status,orders.complaints_status,orders.item_id,orders.pic_path,total_fee,discount_fee,buyer_rate,adjust_fee,orders.spec_nature_info,activity";
        $trade = app::get('topm')->rpcCall('trade.get',$params,'buyer');

        $trade['is_buyer_rate'] = false;
        foreach( $trade['orders'] as $orderListData )
        {
            if( !$orderListData['aftersales_status'] && $trade['buyer_rate'] == '0' && $trade['status'] == 'TRADE_FINISHED' )
            {
                $trade['is_buyer_rate'] = true;
                break;
            }
        }
        $pagedata['trade'] = $trade;
        $pagedata['title'] = "订单详情";  //标题
        return $this->page('topm/member/trade/detail.html', $pagedata);
    }

    public function ajaxGetLogi()
    {
        $tid = input::get('tid');
        $pagedata['logi'] = app::get('topm')->rpcCall('delivery.logistics.tracking.get',array('tid'=>$tid));
        return view::make('topm/member/trade/logistics.html', $pagedata);
    }

    /**
     * @brief 订单取消页面
     *
     * @return
     */
    public function cancel()
    {
        $this->setLayoutFlag('order_detail');
        $pagedata['tid'] = $params['tid'] = input::get('tid');
        $params['user_id'] = userAuth::id();
        $params['fields'] = "status,post_fee,payment";
        $pagedata['trade'] = app::get('topm')->rpcCall('trade.get',$params);
        $pagedata['reason'] = config::get('tradeCancelReason');
        $pagedata['title'] = "取消订单";  //标题
        return $this->page('topm/member/trade/status/cancel.html', $pagedata);

    }

    /**
     * @brief 订单完成页面
     *
     * @return
     */
    public function confirm()
    {
        $this->setLayoutFlag('order_detail');
        $pagedata['tid'] = $params['tid'] = input::get('tid');
        $params['user_id'] = userAuth::id();
        $params['fields'] = "status,post_fee,payment";
        $pagedata['trade'] = app::get('topm')->rpcCall('trade.get',$params);
        return $this->page('topm/member/trade/status/confirm.html', $pagedata);

    }

    /**
     * @brief 订单完成操作
     *
     * @return
     */
    public function confirmReceipt()
    {
        $params['tid'] = input::get('tid');
        $params['user_id'] = userAuth::id();
        try
        {
            app::get('topm')->rpcCall('trade.confirm',$params,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
        $url = url::action('topm_ctl_member_trade@index');
        $msg = app::get('topm')->_('订单确认收货完成');
        return $this->splash('success',$url,$msg,true);
    }


    /**
     * @brief 订单取消操作
     *
     * @return
     */
    public function cancelBuyer()
    {
        $this->setLayoutFlag('cart');
        $reasonSetting = config::get('tradeCancelReason');
        $reasonPost = input::get('cancel_reason');
        if($reasonPost == "other")
        {
            $cancelReason = input::get('other_reason');
        }
        else
        {
            $cancelReason = $reasonSetting['user'][$reasonPost];
        }

        $params['tid'] = input::get('tid');
        $params['user_id'] = userAuth::id();
        $params['cancel_reason'] = $cancelReason;
        try
        {
            app::get('topm')->rpcCall('trade.cancel',$params,'buyer');
        }
        catch(Exception $e)
        {
            $pagedata['msg'] = $e->getMessage();
            //return $this->splash('error',null,$msg,true);
            $pagedata['cancelerror'] = true;
        }
        $pagedata['url'] = url::action('topm_ctl_member_trade@index');
        // app::get('topm')->_('订单取消成功');
        return $this->page('topm/member/trade/status/result.html', $pagedata);
    }


    /**
     * 分页处理
     * @param int $current 当前页
     *
     * @return $pagers
     */
    private function __pages($current,$filter,$count)
    {
        //处理翻页数据
        $current = ($current && $current <= 100 ) ? $current : 1;

        if( $count > 0 ) $totalPage = ceil($count/$this->limit);
        $pagers = array(
            'link'=>url::action('topm_ctl_member_trade@ajaxTradeShow',$filter),
            'current'=>$current,
            'total'=>$totalPage,
        );
        return $pagers;
    }

    public function ajaxTradeShow()
    {
        $postdata = input::get();
        $pagedata = $this->__getTrade($postdata);
        $data['html'] = view::make('topm/member/trade/listitem.html',$pagedata)->render();
        $data['pagers'] = $pagedata['pagers'];
        $data['success'] = true;
        return response::json($data);exit;
    }

    private function __getTrade($postdata)
    {

        if(isset($postdata['s']) && $postdata['s'] )
        {
            if($postdata['s'] == 4)
            {
                $status = 'TRADE_FINISHED';
                $params['buyer_rate'] = 0;
            }
            else
            {
                $status =$this->tradeStatus[$postdata['s']];
            }
        }
        else
        {
            $postdata['s'] = 0;
        }

        $params['status'] = $status;
        $params['user_id'] = userAuth::id();
        $params['page_no'] = $postdata['pages'] ? $postdata['pages'] : 1;
        $params['page_size'] = $this->limit;
        $params['order_by'] = 'created_time desc';
        $params['fields'] = 'tid,shop_id,user_id,status,payment,total_fee,itemnum,post_fee,payed_fee,receiver_name,created_time,receiver_mobile,discount_fee,need_invoice,adjust_fee,order.title,order.price,order.num,order.pic_path,order.tid,order.oid,order.aftersales_status,buyer_rate,order.complaints_status,order.item_id,order.shop_id,order.status,activity';

        $tradelist = app::get('topm')->rpcCall('trade.get.list',$params);
        $count = $tradelist['count'];
        $tradelist = $tradelist['list'];

        foreach( $tradelist as $key=>$row)
        {
            $tradelist[$key]['is_buyer_rate'] = false;

            foreach( $row['order'] as $orderListData )
            {
                if( !$orderListData['aftersales_status'] && $row['buyer_rate'] == '0' && $row['status'] == 'TRADE_FINISHED' )
                {
                    $tradelist[$key]['is_buyer_rate'] = true;
                    break;
                }
            }

            unset($tradelist[$key]['order']);
            $tradelist[$key]['order'][0] = $row['order'];

            if( !$tradelist[$key]['is_buyer_rate'] && $postdata['s'] == 4 )
            {
                unset($tradelist[$key]);
            }
        }

        $pagedata['trades'] = $tradelist;
        $pagedata['pagers'] = $this->__pages($postdata['pages'],$postdata,$count);
        $pagedata['count'] = $count;
        $pagedata['title'] = "我的订单";  //标题
        $pagedata['status'] =$postdata['s'];  //状态
        return $pagedata;

    }


}
