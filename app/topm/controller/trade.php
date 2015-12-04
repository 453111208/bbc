<?php
class topm_ctl_trade extends topm_controller{

    var $noCache = true;

    public function __construct(&$app)
    {
        parent::__construct();
        theme::setNoindex();
        theme::setNoarchive();
        theme::setNofolow();
        theme::prependHeaders('<meta name="robots" content="noindex,noarchive,nofollow" />\n');
        $this->title=app::get('topm')->_('订单中心');
        // 检测是否登录
        if( !userAuth::check() )
        {
            redirect::action('topm_ctl_passport@signin')->send();exit;
        }
    }

    public function tradeDetail()
    {
        echo "detail";
    }

    public function create()
    {

        $postData = input::get();
        $postData['mode'] = $postData['mode'] ? $postData['mode'] :'cart';

        $cartFilter['mode'] = $postData['mode'];
        $cartFilter['needInvalid'] = false;
        $cartFilter['platform'] = 'wap';

        $cartInfo = app::get('topm')->rpcCall('trade.cart.getBasicCartInfo', $cartFilter, 'buyer');
        // 校验购物车是否为空
        if (!$cartInfo)
        {
            $msg = app::get('topm')->_("购物车信息为空或者未选择商品");
            return $this->splash('false', '', $msg, true);
        }
        // 校验购物车是否发生变化
        $md5CartInfo = md5(serialize(utils::array_ksort_recursive($cartInfo, SORT_STRING)));

        if( $postData['md5_cart_info'] != $md5CartInfo )
        {
            $msg = app::get('topm')->_("购物车数据发生变化，请刷新后确认提交");
            return $this->splash('false', '', $msg, true);
        }
        unset($postData['md5_cart_info']);

        if(!$postData['addr_id'])
        {
            $msg .= app::get('topm')->_("请先确认收货地址");
            return $this->splash('success', '', $msg, true);
        }
        else
        {
            $addr = app::get('topm')->rpcCall('user.address.info',array('addr_id'=>$postData['addr_id'],'user_id'=>userAuth::id()));
            list($regions,$region_id) = explode(':',$addr['area']);
            list($state,$city,$district) = explode('/',$regions);

            if (!$state )
            {
                $msg .= app::get('topm')->_("收货地区不能为空！")."<br />";
            }

            if (!$addr['addr'])
            {
                $msg .= app::get('topm')->_("收货地址不能为空！")."<br />";
            }

            if (!$addr['name'])
            {
                $msg .= app::get('topm')->_("收货人姓名不能为空！")."<br />";
            }

            if (!$addr['mobile'] && !$addr['phone'])
            {
                $msg .= app::get('topm')->_("手机或电话必填其一！")."<br />";
            }

            if (strpos($msg, '<br />') !== false)
            {
                $msg = substr($msg, 0, strlen($msg) - 6);
            }
            if($msg)
            {
                return $this->splash('false', '', $msg, true);
            }
         }
        if(!$postData['payment_type'])
        {
            $msg = app::get('topm')->_("请先确认支付类型");
            return $this->splash('success', '', $msg, true);
        }
        else
        {
            $postData['payment_type'] = $postData['payment_type'] ? $postData['payment_type'] : 'online';
        }

        //发票信息
        if($postData['invoice'])
        {
            foreach($postData['invoice'] as $key=>$val)
            {
                $postData[$key] = $val;
            }
            unset($postData['invoice']);
        }

        //店铺配送方式处理
        $shipping = "";
        if( $postData['shipping'])
        {
            foreach($postData['shipping'] as $k=>$v)
            {
                $shipping .= $k.":".$v['template_id'].";";
                if($v['template_id'] == 0)
                {
                    if(!$postData['ziti'][$k]['ziti_addr'])
                    {
                        $msg = app::get('topc')->_("您已选择自提，请选择自提地址");
                        return $this->splash('error', '', $msg, true);
                    }
                    $zitiAddr = app::get('topc')->rpcCall('logistics.ziti.get',array('id'=>$postData['ziti'][$k]['ziti_addr']));
                    $ziti .= $k.":".$zitiAddr['area'].$zitiAddr['addr'].";";
                }

                if( $v['template_id'] == '-1' )
                {
                    $msg = app::get('topc')->_("请选择店铺配送方式");
                    return $this->splash('error', '', $msg, true);
                }
            }
            unset($postData['shipping']);
            unset($postData['ziti']);
        }
        $postData['shipping'] = $shipping;
        if($ziti)
        {
            $postData['ziti'] = $ziti;
        }
        $postData['source_from'] = 'wap';

        $obj_filter = kernel::single('topm_site_filter');
        $postData = $obj_filter->check_input($postData);

        $postData['user_id'] = userAuth::id();
        $postData['user_name'] = userAuth::getLoginName();

        try
        {
           $createFlag = app::get('topm')->rpcCall('trade.create',$postData,'buyer');
        }
        catch(Exception $e)
        {
            return $this->splash('error',null,$e->getMessage(),true);
        }

        try{
            if($postData['payment_type'] == "online")
            {
                $params['tid'] = $createFlag;
                $params['user_id'] = userAuth::id();
                $paymentId = kernel::single('topm_payment')->getPaymentId($params);
                $redirect_url = url::action('topm_ctl_paycenter@index',array('payment_id'=>$paymentId,'merge'=>true));
            }
            else
            {
                $redirect_url = url::action('topm_ctl_paycenter@index',array('tid' => implode(',',$createFlag)));
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            $url = url::action('topm_ctl_member_trade@tradeList');
            return $this->splash('error',$url,$msg,true);
        }
        return $this->splash('success',$redirect_url,'订单创建成功',true);
    }
}
