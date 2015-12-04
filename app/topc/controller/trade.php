<?php
class topc_ctl_trade extends topc_controller{

    var $noCache = true;

    public function __construct(&$app)
    {
        parent::__construct();
        theme::setNoindex();
        theme::setNoarchive();
        theme::setNofolow();
        theme::prependHeaders('<meta name="robots" content="noindex,noarchive,nofollow" />\n');
        $this->title=app::get('topc')->_('订单中心');
        // 检测是否登录
        if( !userAuth::check() )
        {
            redirect::action('topc_ctl_passport@signin')->send();exit;
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
        $cartFilter['platform'] = 'pc';

        $cartInfo = app::get('topc')->rpcCall('trade.cart.getBasicCartInfo', $cartFilter, 'buyer');
        // 校验购物车是否为空
        if (!$cartInfo)
        {
            $msg = app::get('topc')->_("购物车信息为空或者未选择商品");
            return $this->splash('false', '', $msg, true);
        }
        // 校验购物车是否发生变化
        $md5CartInfo = md5(serialize(utils::array_ksort_recursive($cartInfo, SORT_STRING)));
        if( $postData['md5_cart_info'] != $md5CartInfo )
        {
            $msg = app::get('topc')->_("购物车数据发生变化，请刷新后确认提交");
            return $this->splash('false', '', $msg, true);
        }
        unset($postData['md5_cart_info']);

        if(!$postData['addr_id'])
        {
            $msg = app::get('topc')->_("请先确认收货地址");
            return $this->splash('success', '', $msg, true);
        }
        else
        {
            $addr = app::get('topc')->rpcCall('user.address.info',array('addr_id'=>$postData['addr_id'],'user_id'=>userAuth::id()));
            list($regions,$region_id) = explode(':',$addr['area']);
            list($state,$city,$district) = explode('/',$regions);

            $validator = validator::make(
                ['state' => $state,
                 'addr' => $addr['addr'] ,
                 'name' => $addr['name'],
                 'mobile' => $addr['mobile']
                ],
                [
                'state' => 'required',
                'addr' => 'required',
                'name' => 'required',
                'mobile' => 'required|mobile'
                ],
                [
                 'state' => '收货地区不能为空!',
                 'addr' => '收货地址不能为空!',
                 'name' => '收货人姓名不能为空！',
                 'mobile' => '手机号码必填!|手机号码格式不正确!'
                ]
            );
            if ($validator->fails())
            {
                $messages = $validator->messagesInfo();

                foreach( $messages as $error )
                {
                    return $this->splash('error',null,$error[0]);
                }
            }

        }

        if(!$postData['payment_type'])
        {
            $msg = app::get('topc')->_("请先确认支付类型");
            return $this->splash('success', '', $msg, true);
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
        else
        {
            $msg = app::get('topc')->_("请选择店铺配送方式");
            return $this->splash('error', '', $msg, true);
        }
        $postData['shipping'] = $shipping;
        if($ziti)
        {
            $postData['ziti'] = $ziti;
        }
        $postData['source_from'] = 'pc';

        $obj_filter = kernel::single('topc_site_filter');
        $postData = $obj_filter->check_input($postData);

        $postData['user_id'] = userAuth::id();
        $postData['user_name'] = userAuth::getLoginName();

        try
        {
           $createFlag = app::get('topc')->rpcCall('trade.create',$postData,'buyer');
        }
        catch(Exception $e)
        {
            return $this->splash('error',null,$e->getMessage(),true);
        }

        try
        {
            if($postData['payment_type'] == "online")
            {
                $params['tid'] = $createFlag;
                $params['user_id'] = userAuth::id();
                $paymentId = kernel::single('topc_payment')->getPaymentId($params);
                $redirect_url = url::action('topc_ctl_paycenter@index',array('payment_id'=>$paymentId,'merge'=>true));
            }
            else
            {
                $redirect_url = url::action('topc_ctl_paycenter@index',array('tid' => implode(',',$createFlag)));
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            $url = url::action('topc_ctl_member_trade@tradeList');
            return $this->splash('success',$url,$msg,true);
        }
        return $this->splash('success',$redirect_url,'订单创建成功',true);
    }
}


