<?php
class systrade_data_trade_create{

   /**
     * 构造方法
     * @param object app
     */
    public function __construct($app)
    {
        $this->objMath = kernel::single('ectools_math');
    }

    /**
     * 订单标准数据生成
     * @params mixed - 订单数据
     * @params string - 唯一标识
     * @params string message
     * @param array cart object array
     * @return boolean - 成功与否(mixed 订单数据)
     */
    public function generate(&$sdf, &$msg='',$aCart=array())
    {
        try
        {
            $db = app::get('systrade')->database();
            $db->beginTransaction();

            $orderData = array();
            // 格式化订单数据，库存修改
            $is_generate = $this->_chgdata($sdf, $orderData, $msg, $aCart);
            if (!$is_generate)
            {
                return false;
            }

            //获取生成的订单号
            foreach($orderData as $key=>$value)
            {
                $orders[$key] = $value['tid'];
            }


            // 保存订单数据
            $flag = $this->save($orderData, $msg);
            if(!$flag)
            {
                throw new \LogicException(app::get('systrade')->_('订单生成失败'));
            }

            if($sdf['cartIds'])
            {
                $cartIds = $sdf['cartIds'];
                if(isset($sdf['cartIds']) && is_array($sdf['cartIds']))
                {
                    $cartIds = implode(',',$sdf['cartIds']);
                }
                $delCartResult = app::get('systrade')->rpcCall('trade.cart.delete', array('cart_id'=>$cartIds,'mode'=>$sdf['mode'],'user_id'=>$sdf['user_id']), 'buyer');
                if($delCartResult === false)
                {
                    throw new \LogicException(app::get('systrade')->_('删除购物车数据错误'));
                }
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return $orders;
    }

    /**
     * @params array sdf
     * @params array
     * @params string
     * @params string message
     * @return boolean true or false
     */
    private function _chgdata(&$sdf, &$order_data, &$msg='',$aCart=array())
    {
        $sdf_trade['user_id'] = $sdf['user_id'];
        $sdf_trade['region_id'] = $sdf['region_id'];
        $sdf_trade['shipping'] = $sdf['shipping'];

        $now = time();
        $tradeBaseTime = date('ymdHi');
        $tradeBaseRandNum = rand(0,49);//str_pad($tradeBaseRandNum,2,'0',STR_PAD_LEFT);
        $tradeModUserId = str_pad($sdf['user_id']%10000,4,'0',STR_PAD_LEFT);
        $shopCount = count($aCart['resultCartData']);

        if($sdf['payment_type'] == "online")
        {
            $status = "WAIT_BUYER_PAY";
        }
        elseif($sdf['payment_type'] == "offline")
        {
            $status = "WAIT_SELLER_SEND_GOODS";
        }
        $sdf['cartIds'] = array(); //用于删除对应的购物车数据
        foreach ($aCart['resultCartData'] as $shop_id => $tval) {
            $tid = $tradeBaseTime.str_pad(++$tradeBaseRandNum,2,'0',STR_PAD_LEFT).$tradeModUserId;
            $order_data[$shop_id] = array(
                'user_id'           => $sdf['user_id'],
                'user_name'           => $sdf['user_name'],
                'shop_id'           => $shop_id,
                'tid'               => $tid,
                'status'            => $status,
                'created_time'      => $now,
                'modified_time'     => $now,
                'trade_memo'        => strip_tags($sdf['trade_memo'][$shop_id]),
                'ip'                => $_SERVER['REMOTE_ADDR'],
                'title'             => app::get('systrade')->_('订单明细介绍'),
                'itemnum'           => $tval['cartCount']['itemnum'],
                'dlytmpl_id'        => $sdf['shipping'][$shop_id]['template_id'],
                'ziti_addr'        => $sdf['ziti'][$shop_id]['ziti_addr'],
                'total_weight'      => $tval['cartCount']['total_weight'],
                'pay_type'          => $sdf['payment_type'] ? $sdf['payment_type'] : 'online',
                'need_invoice'      => ($sdf['invoice']['need_invoice'] ? 1 : 0),
                'trade_from'        => $sdf['source_from'],
                'invoice_name'      => $sdf['invoice']['invoice_title'],
                'invoice_main'      => strip_tags($sdf['invoice']['invoice_content']),
                'invoice_type'      => $sdf['invoice']['invoice_type'],
                'receiver_name'     => $sdf['delivery']['receiver_name'],
                'receiver_address'  => $sdf['delivery']['receiver_address'],
                'receiver_zip'      => $sdf['delivery']['receiver_zip'],
                'receiver_tel'      => $sdf['delivery']['receiver_tel'],
                'receiver_mobile'   => $sdf['delivery']['receiver_mobile'],
                'receiver_state'    => $sdf['delivery']['receiver_state'],
                'receiver_city'     => $sdf['delivery']['receiver_city'],
                'receiver_district' => $sdf['delivery']['receiver_district'],
                'buyer_area'           => $sdf['delivery']['buyer_area'],
            );

            $objLibCatServiceRate = kernel::single('sysshop_data_cat');
            /*
            if( !$sdf['shipping'][$shop_id]['template_id'] )
            {
                throw new \LogicException(app::get('systrade')->_('请选择店铺配送方式'));
            }
             */
            //计算订单总金额
            $totalParams = array(
                'discount_fee' => $tval['cartCount']['total_discount'],
                'total_fee' => $tval['cartCount']['total_fee'],
                'total_weight' => $tval['cartCount']['total_weight'],
                'shop_id' => $tval['shop_id'],
                'template_id' => $sdf['shipping'][$shop_id]['template_id'],
                'region_id' => str_replace('/', ',', $sdf['region_id']),
                'usedCartPromotionWeight' => $tval['usedCartPromotionWeight'],
            );
            $objLibTradeTotal = kernel::single('systrade_data_trade_total');
            $totalInfo= $objLibTradeTotal->trade_total_method($totalParams);

            $order_data[$shop_id]['payment'] = $totalInfo['payment'];
            $order_data[$shop_id]['total_fee'] = $totalInfo['total_fee'];
            $order_data[$shop_id]['discount_fee'] = $totalInfo['discount_fee'];
            $order_data[$shop_id]['obtain_point_fee'] = $totalInfo['obtain_point_fee'];
            $order_data[$shop_id]['post_fee'] = $totalInfo['post_fee'];
            // 用于生成促销日志表
            $order_data[$shop_id]['basicPromotionListInfo'] = $tval['basicPromotionListInfo'];
            if($tval['cartCount']['total_coupon_discount']>0)
            {
                $order_data[$shop_id]['useCoupon'] = true;
            }
            // 促销信息数组
            $order_data[$shop_id]['basicPromotionListInfo'] = $tval['basicPromotionListInfo'];
            // 本次购物使用的促销id
            $order_data[$shop_id]['usedCartPromotion'] = $tval['usedCartPromotion'];
            // 子订单
            foreach($tval['object'] as $k =>$oval){
                $oid = $tradeBaseTime.str_pad(++$tradeBaseRandNum,2,'0',STR_PAD_LEFT).$tradeModUserId;
                $order_data[$shop_id]['order'][$k] = array(
                    'oid'              => $oid,
                    'tid'              => $tid,
                    'shop_id'          => $shop_id,
                    'user_id'          => $sdf['user_id'],
                    'item_id'          => $oval['item_id'],
                    'sku_id'           => $oval['sku_id'],
                    'bn'               => $oval['bn'],
                    'price'            => $oval['price']['price'],
                    'num'              => $oval['quantity'],
                    'payment'          => $oval['price']['total_price'],
                    'total_fee'        => $oval['price']['total_price'],
                    'pic_path'         => $oval['image_default_id'],
                    'sub_stock'        => $oval['sub_stock'],
                    'cat_service_rate' => $objLibCatServiceRate->getCatServiceRate(array('shop_id'=>$shop_id, 'cat_id'=>$oval['cat_id'])),
                    'sendnum'          => 0,
                    'created_time'     => $now,
                    'modified_time'    => $now,
                    'status'           => $status,
                    'title'            => $oval['title'],
                    'spec_nature_info' => $oval['spec_info'],
                    'order_from'       => $sdf['source_from'],
                    'selected_promotion' => $oval['selected_promotion'],
                );
                if($oval['promotion_type']=='activity')
                {
                    $order_data[$shop_id]['order'][$k]['promotion_type'] = $oval['promotion_type'];
                    $order_data[$shop_id]['order'][$k]['activityDetail'] = $oval['activityDetail'];
                }
                // 处理sku订单冻结
                $arrParams = array(
                    'item_id' => $oval['item_id'],
                    'sku_id' => $oval['sku_id'],
                    'quantity' => $oval['quantity'],
                    'sub_stock' => $oval['sub_stock'],
                    'status' => 'on',
                );
                $is_minus = app::get('systrade')->rpcCall('item.store.minus',$arrParams);
                $sdf['cartIds'][] = $oval['cart_id']; //处理的cart_id,用于删除购物车对应数据
                $j++;
            }

            $i++;
        }

        return $order_data;
    }

    /**
     * 订单保存
     * @param array sdf
     * @param string member indent
     * @param string message
     * @return boolean success or failure
     */
    public function save(&$sdf, &$msg='')
    {
        // 创建订单是和中心的交互
        $objMdlTrade = app::get('systrade')->model('trade');
        foreach ($sdf as $trade)
        {
            $result = $objMdlTrade->save($trade,null,true);
            // 日志
            $logFlag = $this->addLog($trade, $result);
            if(!$logFlag)
            {
                $msg = app::get('systrade')->_("订单生成失败[日志]！");
                return false;
            }
            // 促销记录日志
            $logFlag = $this->addPromotionLog($trade);
            if(!$logFlag)
            {
                $msg = app::get('systrade')->_("订单生成失败[促销日志]！");
                return false;
            }

            // 优惠券促销记录日志
            if($trade['useCoupon'])
            {
                $logFlag = $this->addCouponUseLog($trade);
                if(!$logFlag)
                {
                    $msg = app::get('systrade')->_("订单生成失败[优惠券促销日志]！");
                    return false;
                }
            }
            if(!$result)
            {
                $msg = app::get('systrade')->_("订单生成失败！");
                return false;
            }
        }

        return true;
    }

    /**
     * 记录订单的优惠信息表
     * @param array &$tradeData 生成的订单数据
     */
    private function addPromotionLog(&$tradeData)
    {
        $objMdlPromDetail = app::get('systrade')->model('promotion_detail');
        foreach($tradeData['order'] as $orderData)
        {
            if( in_array($orderData['selected_promotion'], $tradeData['usedCartPromotion']) )
            {
                $promLogData = array(
                    'tid'   => $orderData['tid'],
                    'oid'   => $orderData['oid'],
                    'user_id'    => $orderData['user_id'],
                    'item_id'    => $orderData['item_id'],
                    'sku_id'    => $orderData['sku_id'],
                    'promotion_id' => $orderData['selected_promotion'],
                    'promotion_type' => $tradeData['basicPromotionListInfo'][$orderData['selected_promotion']]['promotion_type'],
                    // 'discount_fee' => $tradeData['discount_fee'],
                    'promotion_tag' => $tradeData['basicPromotionListInfo'][$orderData['selected_promotion']]['promotion_tag'],
                    'promotion_name' => $tradeData['basicPromotionListInfo'][$orderData['selected_promotion']]['promotion_name'],
                    'promotion_desc' => $tradeData['basicPromotionListInfo'][$orderData['selected_promotion']]['promotion_desc'],
                );
                $logFlag = $objMdlPromDetail->save($promLogData);
                if(!$logFlag)
                {
                    return false;
                }
            }
            if($orderData['activityDetail'])
            {
                $promLogData = array(
                    'tid'   => $orderData['tid'],
                    'oid'   => $orderData['oid'],
                    'user_id'    => $orderData['user_id'],
                    'item_id'    => $orderData['item_id'],
                    'sku_id'    => $orderData['sku_id'],
                    'promotion_id' => $orderData['activityDetail']['activity_id'],
                    'promotion_type' => 'activity',
                    // 'discount_fee' => $tradeData['discount_fee'],
                    'promotion_tag' => $orderData['activityDetail']['activity_info']['activity_tag'],
                    'promotion_name' => $orderData['activityDetail']['activity_info']['activity_name'],
                    'promotion_desc' => $tradeData['activityDetail']['activity_info']['description'],
                );
                $logFlag = $objMdlPromDetail->save($promLogData);
                if(!$logFlag)
                {
                    return false;
                }

                // 修改活动商品表的销量字段值
                $db = app::get('syspromotion')->model('activity_item')->database();
                $sqlStr = "UPDATE syspromotion_activity_item SET sales_count=ifnull(sales_count,0)+? WHERE  item_id=? AND activity_id=?";
                if ($db->executeUpdate($sqlStr, [$orderData['num'], $orderData['item_id'], $orderData['activityDetail']['activity_id']])==0)
                {
                    return false;
                }
            }

        }

        return true;
    }

    /**
     * 记录订单使用优惠券记录
     * @param array &$tradeData 生成的订单数据
     * @param bool $flag       订单生成成功标识
     */
    private function addCouponUseLog($tradeData)
    {
        $objMdlCart = app::get('systrade')->model('cart');
        $userIdent = $objMdlCart->getUserIdentMd5();
        $coupon_code = $_SESSION['cart_use_coupon'][$userIdent][$tradeData['shop_id']];
        $data = array(
            'tid' => $tradeData['tid'],
            'coupon_code' => $coupon_code,
        );
        if(!app::get('systrade')->rpcCall('user.coupon.useLog', $data))
        {
            return false;
        }
        unset($_SESSION['cart_use_coupon'][$userIdent][$tradeData['shop_id']]);
        return true;
    }

    /**
     * 记录订单创建日志
     * @param array &$tradeData 生成的订单数据
     * @param bool $flag       订单生成成功标识
     */
    private function addLog(&$tradeData, &$flag)
    {
        $objLibLog = kernel::single('systrade_data_trade_log');
        $logText = $flag ? '订单创建成功！' : '订单创建失败！';
        $sdfTradeLog = array(
            'rel_id'   => $tradeData['tid'],
            'op_id'    => $tradeData['user_id'],
            'op_name'  => !$tradeData['user_name'] ? app::get('systrade')->_('买家') : $tradeData['user_name'],
            'op_role'  => 'buyer',
            'behavior' => 'create',
            'log_text' => $logText,
        );

        $logFlag = $objLibLog->addLog($sdfTradeLog);
        if(!$logFlag)
        {
            return false;
        }
        return true;
    }

}


