<?php
class topc_ctl_member_trade extends topc_ctl_member {

    public function tradeList()
    {
        $user_id = userAuth::id();
        $postdata = input::get();
        if(input::get('status'))
        {
            $status =input::get('status');
        }
        $params = array(
            'user_id' => userAuth::id(),
            'status' => $status,
            'page_no' =>$postdata['pages'] ? $postdata['pages'] : 1,
            'page_size' =>$this->limit,
            'order_by' =>'created_time desc',
            'fields' =>'tid,shop_id,user_id,status,payment,total_fee,post_fee,payed_fee,receiver_name,created_time,receiver_mobile,discount_fee,need_invoice,adjust_fee,order.title,order.price,order.num,order.pic_path,order.tid,order.oid,order.aftersales_status,buyer_rate,order.complaints_status,order.item_id,order.shop_id,order.status,order.spec_nature_info,activity,pay_type',
        );
        $tradelist = app::get('topc')->rpcCall('trade.get.list',$params,'buyer');
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
        }
        $pagedata['trades'] = $tradelist;
        $pagedata['pagers'] = $this->__pages($postdata['pages'],$postdata,$count);
        $pagedata['count'] = $count;
        $pagedata['action'] = 'topc_ctl_member_trade@tradeList';
        $this->action_view = "trade/list.html";
        return $this->output($pagedata);
    }

    public function tradeDetail()
    {
        $params['tid'] = input::get('tid');
        $params['user_id'] = userAuth::id();
        $params['fields'] = "tid,status,payment,post_fee,pay_type,payed_fee,receiver_state,receiver_city,receiver_district,receiver_address,trade_memo,receiver_name,receiver_mobile,ziti_addr,orders.price,orders.num,orders.title,orders.item_id,orders.pic_path,total_fee,discount_fee,buyer_rate,adjust_fee,orders.total_fee,orders.adjust_fee,created_time,shop_id,need_invoice,invoice_name,invoice_type,invoice_main,activity";
        $trade = app::get('topc')->rpcCall('trade.get',$params,'buyer');
        $pagedata['trade'] = $trade;
        $pagedata['action'] = 'topc_ctl_member_trade@tradeList';
        $this->action_view = "trade/detail.html";
        return $this->output($pagedata);
    }

    public function ajaxGetLogi()
    {
        $tid = input::get('tid');
        $pagedata['logi'] = app::get('topc')->rpcCall('delivery.logistics.tracking.get',array('tid'=>$tid));
        return view::make('topc/member/trade/logistics.html', $pagedata);
    }


    public function ajaxCancelTrade()
    {
        $pagedata['tid'] = input::get('tid');
        $pagedata['reason'] = config::get('tradeCancelReason');
        return view::make('topc/member/gather/cancel.html', $pagedata);
    }

    public function ajaxConfirmTrade()
    {
        $pagedata['tid'] = input::get('tid');
        return view::make('topc/member/gather/confirm.html', $pagedata);
    }


    public function cancelOrderBuyer()
    {
        $reasonSetting = config::get('tradeCancelReason');
        $reasonPost = input::get('cancel_reason');
        $validator = validator::make($reasonPost,['required'],['取消原因必选!']);
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();
            foreach( $messages as $error )
            {
                return $this->splash('error',null,$error[0]);
            }
        }
        if($reasonPost == "other")
        {
            $cancelReason = input::get('other_reason');
            $validator = validator::make($cancelReason,['required'],['取消原因必须填写!']);
            if ($validator->fails())
            {
                $messages = $validator->messagesInfo();
                foreach( $messages as $error )
                {
                    return $this->splash('error',null,$error[0]);
                }
            }
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
            app::get('topc')->rpcCall('trade.cancel',$params,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
        $url = url::action('topc_ctl_member_trade@tradeList');
        $msg = app::get('topc')->_('订单取消成功');
        return $this->splash('success',$url,$msg,true);
    }

    public function confirmReceipt()
    {
        $params['tid'] = input::get('tid');
        $params['user_id'] = userAuth::id();
        try
        {
            app::get('topc')->rpcCall('trade.confirm',$params,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
        $url = url::action('topc_ctl_member_trade@tradeList');
        $msg = app::get('topc')->_('订单确认收货完成');
        return $this->splash('success',$url,$msg,true);
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
        $current = $current ? $current : 1;
        $filter['pages'] = time();
        $limit = $this->limit;

        if( $count > 0 ) $totalPage = ceil($count/$limit);
        $pagers = array(
            'link'=>url::action('topc_ctl_member_trade@tradeList',$filter),
            'current'=>$current,
            'total'=>$totalPage,
            'token'=>time(),
        );
        return $pagers;
    }
}
