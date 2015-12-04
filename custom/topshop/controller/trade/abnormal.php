<?php
class topshop_ctl_trade_abnormal extends topshop_controller {

    public function index()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('异常订单取消列表');

        $data = app::get('topshop')->rpcCall('trade.abnormal.list.get', ['role'=>'seller','fields'=>'*']);
        $pagedata['tradeAnormal'] = $data['tradeAnormal'];

        $total = $pagedata['total_results'];

        return $this->page('topshop/trade/abnormal/list.html', $pagedata);
    }

    public function detail()
    {
    	return $this->page('topshop/trade/abnormal/detail.html');
    }

    /**
     * 显示取消异常订单页面
     */
    public function closeView()
    {
        $pagedata['tid'] = input::get('tid');
        $pagedata['reason'] = config::get('tradeCancelReason.shopuser');

        return view::make('topshop/trade/abnormal/cancel.html', $pagedata);
    }

    public function applyClose()
    {
        $reasonSetting = config::get('tradeCancelReason.shopuser');
        $reasonPost = input::get('cancel_reason');
        if($reasonPost == "other")
        {
            $cancelReason = input::get('other_reason');
        }
        else
        {
            $cancelReason = $reasonSetting[$reasonPost];
        }
        $params['tid'] = input::get('tid');
        $params['reason'] = $cancelReason;
        $url = url::action('topshop_ctl_trade_abnormal@index');
        try
        {
            $result = app::get('topshop')->rpcCall('trade.abnormal.create', $params);
        }
        catch(Exception $e)
        {
            $result = false;
        }

        if( !$result )
        {
            $msg = $e->getMessage();
            return $this->splash('error',"",$msg,true);
        }

        $msg = '申请提交成功';
        return $this->splash('succecc',$url,$msg,true);
    }

}


