<?php
class topc_ctl_paycenter extends topc_controller{
    public function __construct($app)
    {
        parent::__construct();
        $this->setLayoutFlag('paycenter');
        // 检测是否登录
    }

    public function index()
    {
        $filter = input::get();
        if(isset($filter['tid']) && $filter['tid'])
        {
            $pagedata['payment_type'] = "offline";
            $ordersMoney = app::get('topc')->rpcCall('trade.money.get',array('tid'=>$filter['tid']),'buyer');

            if($ordersMoney)
            {
                foreach($ordersMoney as $key=>$val)
                {
                    $newOrders[$val['tid']] = $val['payment'];
                    $newMoney += $val['payment'];
                }
                $paymentBill['money'] = $newMoney;
                $paymentBill['cur_money'] = $newMoney;
            }
            $pagedata['trades'] = $paymentBill;
            $pagedata['mainfile'] = "topc/payment/payment.html";
            return $this->page('topc/payment/index.html', $pagedata);
        }

        if($filter['newtrade'])
        {
            $newtrade = $filter['newtrade'];
            unset($filter['newtrade']);
        }

        if($filter['merge'])
        {
            $ifmerge = $filter['merge'];
            unset($filter['merge']);
        }

        //获取可用的支付方式列表
        $payType['platform'] = 'ispc';
        $payments = app::get('topc')->rpcCall('payment.get.list',$payType,'buyer');
        $filter['fields'] = "*";
        $paymentBill = app::get('topc')->rpcCall('payment.bill.get',$filter,'buyer');

        //检测订单中的金额是否和支付金额一致 及更新支付金额
        $trade = $paymentBill['trade'];
        $tids['tid'] = implode(',',array_keys($trade));
        $ordersMoney = app::get('topc')->rpcCall('trade.money.get',$tids,'buyer');

        if($ordersMoney)
        {
            foreach($ordersMoney as $key=>$val)
            {
                $newOrders[$val['tid']] = $val['payment'];
                $newMoney += $val['payment'];
            }
            $result = array(
                'trade_own_money' => json_encode($newOrders),
                'money' => $newMoney,
                'cur_money' => $newMoney,
                'payment_id' => $filter['payment_id'],
            );

            if($newMoney != $paymentBill['cur_money'])
            {
                try{
                    app::get('topc')->rpcCall('payment.money.update',$result);
                }
                catch(Exception $e)
                {
                    $msg = $e->getMessage();
                    $url = url::action('topc_ctl_member_trade@tradeList');
                    return $this->splash('error',$url,$msg,true);
                }
                $paymentBill['money'] = $newMoney;
                $paymentBill['cur_money'] = $newMoney;
            }
        }

        $pagedata['tids'] = $tids['tid'];
        $pagedata['trades'] = $paymentBill;
        $pagedata['payments'] = $payments;
        $pagedata['newtrade'] = $newtrade;
        $pagedata['mainfile'] = "topc/payment/payment.html";
        return $this->page('topc/payment/index.html', $pagedata);
    }

    public function createPay()
    {
        $filter = input::get();
        $filter['user_id'] = userAuth::id();
        if($filter['merge'])
        {
            $ifmerge = $filter['merge'];
            unset($filter['merge']);
        }

        try
        {
            $paymentId = kernel::single('topc_payment')->getPaymentId($filter);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            $url = url::action('topc_ctl_member_trade@tradeList');
            echo '<meta charset="utf-8"><script>alert("'.$msg.'");location.href="'.$url.'";</script>';
            exit;
        }
        $url = url::action('topc_ctl_paycenter@index',array('payment_id'=>$paymentId,'merge'=>$ifmerge));
        return $this->splash('success',$url,$msg,true);
    }

    public function dopayment()
    {
        $postdata = input::get();
        $payment = $postdata['payment'];
        $payment['user_id'] = userAuth::id();
        $payment['platform'] = "pc";
        try
        {
            app::get('topc')->rpcCall('payment.trade.pay',$payment);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            echo '<meta charset="utf-8"><script>alert("'.$msg.'"); window.close();</script>';
            exit;
        }
        $url = url::action('topc_ctl_paycenter@finish',array('payment_id'=>$payment['payment_id']));
        return $this->splash('success',$url,$msg,true);
    }

    public function finish()
    {
        $postdata = input::get();
        try
        {
            $params['payment_id'] = $postdata['payment_id'];
            $params['fields'] = 'payment_id,status,pay_app_id,pay_name,money,cur_money';
            $result = app::get('topc')->rpcCall('payment.bill.get',$params);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
        }
        $result['num'] = count($result['trade']);
        $pagedata['msg'] = $msg;
        $pagedata['payment'] = $result;
        $pagedata['mainfile'] = "topc/payment/finish.html";
        return $this->page('topc/payment/index.html', $pagedata);
    }
}


