<?php
class topm_ctl_paycenter extends topm_controller{

    public function __construct($app)
    {
        parent::__construct();
        //$this->setLayoutFlag('paycenter');
        $this->setLayoutFlag('cart');
        // 检测是否登录
        if( !userAuth::check() )
        {
            redirect::action('topm_ctl_passport@signin')->send();exit;
        }
    }

    public function index($filter=null)
    {
        if(!$filter)
        {
            $filter = input::get();
        }

        if(isset($filter['tid']) && $filter['tid'])
        {
            $pagedata['payment_type'] = "offline";
            $ordersMoney = app::get('topm')->rpcCall('trade.money.get',array('tid'=>$filter['tid']),'buyer');

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
            return $this->page('topm/payment/offline.html', $pagedata);
        }

        if($filter['newtrade'])
        {
            $newtrade = $filter['newtrade'];
            unset($filter['newtrade']);
        }

        //获取可用的支付方式列表
        $payType['platform'] = 'iswap';
        $payments = app::get('topm')->rpcCall('payment.get.list',$payType,'buyer');

        //这里做个埋点，获取openid的
        //因为微信支付太恶心了，要求有用户的openid才能支付，所以做这个埋点临时使用，以后再慢慢看这个埋点怎么弄
        foreach($payments as $paymentKey => $payment)
        {

            if(in_array($payment['app_id'], ['wxpayjsapi']))
            {
                if(!kernel::single('topm_wechat_wechat')->from_weixin())
                {
                    unset($payments[$paymentKey]);
                    continue;
                }

                $payInfo = app::get('topm')->rpcCall('payment.get.conf', ['app_id' => 'wxpayjsapi']);
                $wxAppId = $payInfo['setting']['appId'];
                $wxAppsecret = $payInfo['setting']['Appsecret'];
                if(!input::get('code'))
                {
                    $url = url::action('topm_ctl_paycenter@index',$filter);
                    kernel::single('topm_wechat_wechat')->get_code($wxAppId, $url);
                }
                else
                {
                    $code = input::get('code');
                    $openid = kernel::single('topm_wechat_wechat')->get_openid_by_code($wxAppId, $wxAppsecret, $code);
                    if($openid == null)
                        $this->splash('failed', 'back',  app::get('topm')->_('获取openid失败'));
                    $pagedata['openid'] = $openid;
                }
            }
        }

        $filter['fields'] = "*";
        $paymentBill = app::get('topm')->rpcCall('payment.bill.get',$filter,'buyer');

        //检测订单中的金额是否和支付金额一致 及更新支付金额
        $trade = $paymentBill['trade'];
        $tids['tid'] = implode(',',array_keys($trade));
        $ordersMoney = app::get('topm')->rpcCall('trade.money.get',$tids,'buyer');

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
                    app::get('topm')->rpcCall('payment.money.update',$result);
                }
                catch(Exception $e)
                {
                    $msg = $e->getMessage();
                    $url = url::action('topm_ctl_member_trade@tradeList');
                    return $this->splash('error',$url,$msg,true);
                }
                $trades['money'] = $newMoney;
                $trades['cur_money'] = $newMoney;
            }
        }

        $pagedata['tids'] = $tids['tid'];
        $pagedata['trades'] = $paymentBill;
        $pagedata['payments'] = $payments;
        $pagedata['newtrade'] = $newtrade;
        return $this->page('topm/payment/index.html', $pagedata);
    }

    public function createPay()
    {
        $filter = input::get();
        $filter['user_id'] = userAuth::id();

        try
        {
            $paymentId = kernel::single('topm_payment')->getPaymentId($filter);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            $url = url::action('topm_ctl_member_trade@index');
            echo '<meta charset="utf-8"><script>alert("'.$msg.'");location.href="'.$url.'";</script>';
            exit;
        }
        $url = url::action('topm_ctl_paycenter@index',array('payment_id'=>$paymentId,'merge'=>$ifmerge));
        return $this->splash('success',$url,$msg,true);
    }

    public function dopayment()
    {
        $postdata = input::get();
        $payment = $postdata['payment'];

        if(!$payment['pay_app_id'])
        {
            echo '<meta charset="utf-8"><script>alert("请选择支付方式"); window.close();</script>';
            exit;
        }
        $payment['user_id'] = userAuth::id();
        $payment['platform'] = "wap";
        try
        {
            app::get('topm')->rpcCall('payment.trade.pay',$payment);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            echo '<meta charset="utf-8"><script>alert("'.$msg.'"); window.close();</script>';
            exit();
        }
        $url = url::action('topm_ctl_paycenter@finish',array('payment_id'=>$payment['payment_id']));
        return $this->splash('success',$url,null,true);
    }

    public function finish()
    {
        $postdata = input::get();
        try
        {
            $params['payment_id'] = $postdata['payment_id'];
            $params['fields'] = 'payment_id,status,pay_app_id,pay_name,money,cur_money';
            $result = app::get('topm')->rpcCall('payment.bill.get',$params);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
        }
        $trades = $result['trade_own_money'];
        $result['num'] = count($trades);
        $pagedata['msg'] = $msg;
        $pagedata['payment'] = $result;
        return $this->page('topm/payment/finish.html', $pagedata);
    }
}
