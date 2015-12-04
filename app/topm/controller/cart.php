<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topm_ctl_cart extends topm_controller{

    public $payType = array(
        'online' => '线上支付',
        'offline' => '货到付款',
    );
    public function __construct(&$app)
    {
        parent::__construct();
        // 检测是否登录
        if( !userAuth::check() )
        {
            redirect::action('topm_ctl_passport@signin')->send();exit;
        }
        $this->setLayoutFlag('cart');
    }

    public function index()
    {
        $pagedata['defaultImageId'] = app::get('image')->getConf('image.set');

        $cartData = app::get('topm')->rpcCall('trade.cart.getCartInfo', array('platform'=>'wap', 'user_id'=>userAuth::id()), 'buyer');
        $pagedata['aCart'] = $cartData['resultCartData'];

        $pagedata['totalCart'] = $cartData['totalCart'];

        foreach ($pagedata['aCart'] as &$v) {
            $params = array(
                'page_no' => 0,
                'page_size' => 1,
                'fields' => '*',
                'shop_id' => $v['shop_id'],
                'platform' => 'wap',
                'is_cansend' => 1,
            );
            $couponListData = app::get('topm')->rpcCall('promotion.coupon.list', $params, 'buyer');
            if($couponListData['count']>0)
            {
                $v['hasCoupon'] = 1;
            }
        }

        // 店铺可领取优惠券

        return $this->page('topm/cart/index.html', $pagedata);
    }

    /**
     * @brief 加入购物车
     *
     * @return
     */
    public function add()
    {
        $params['quantity'] = input::get('item.quantity');
        $params['goodsType'] = input::get('obj_type');
        $params['sku_id'] = input::get('item.sku_id');
        $mode = input::get('mode');

        $params['quantity'] = $params['quantity'] ? $params['quantity'] : 1;//购买数量，如果已有购买则累加
        $params['obj_type'] = $params['obj_type'] ? $params['obj_type'] : 'item';
        $params['sku_id'] = intval($params['sku_id']);
        $params['mode'] = $mode ? $mode : 'cart';
        $params['user_id'] = userAuth::id();

        try
        {
            $data = app::get('topm')->rpcCall('trade.cart.add', $params, 'buyer');
            if( $data === false )
            {
                $msg = app::get('topm')->_('加入购物车失败!');
                return $this->splash('error',null,$msg,true);
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }

        $msg = app::get('topm')->_('成功加入购物车');
        if( $params['mode'] == 'fastbuy' )
        {
            $url = url::action('topm_ctl_cart@checkout',array('mode'=>'fastbuy') );
            $msg = "";
        }
        return $this->splash('success',$url,$msg,true);
    }

    public function updateCart()
    {
        $mode = input::get('mode');
        $obj_type = input::get('obj_type','item');
        $postCartId = input::get('cart_id');
        $postCartNum = input::get('cart_num');
        $postPromotionId = input::get('promotionid');

        $params = array();
        foreach ($postCartId as $cartId => $v)
        {
            $data['mode'] = $mode;
            $data['obj_type'] = $obj_type;
            $data['cart_id'] = intval($cartId);
            $data['totalQuantity'] = intval($postCartNum[$cartId]);
            $data['selected_promotion'] = intval($postPromotionId[$cartId]);
            $data['user_id'] = userAuth::id();

            if($v=='1')
            {
                $data['is_checked'] = '1';
            }
            if($v=='0')
            {
                $data['is_checked'] = '0';
            }
            $params[] = $data;
        }

        try
        {
            foreach($params as $updateParams)
            {
                $data = app::get('topm')->rpcCall('trade.cart.update',$updateParams);
                if( $data === false )
                {
                    $msg = app::get('topm')->_('更新失败');
                    return $this->splash('error',null,$msg,true);
                }
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }

        $cartData = app::get('topm')->rpcCall('trade.cart.getCartInfo', array('platform'=>'wap', 'user_id'=>userAuth::id()), 'buyer');
        $pagedata['aCart'] = $cartData['resultCartData'];

        // 临时统计购物车页总价数量等信息
        $totalWeight = 0;
        $totalNumber = 0;
        $totalPrice = 0;
        $totalDiscount = 0;
        foreach($cartData['resultCartData'] as $v)
        {
            $totalWeight += $v['cartCount']['total_weight'];
            $totalNumber += $v['cartCount']['itemnum'];
            $totalPrice += $v['cartCount']['total_fee'];
            $totalDiscount += $v['cartCount']['total_discount'];
        }
        $totalCart['totalWeight'] = $totalWeight;
        $totalCart['number'] = $totalNumber;
        $totalCart['totalPrice'] = $totalPrice;
        $totalCart['totalAfterDiscount'] = ecmath::number_minus(array($totalPrice, $totalDiscount));
        $totalCart['totalDiscount'] = $totalDiscount;
        $pagedata['totalCart'] = $totalCart;

        $pagedata['defaultImageId'] = app::get('image')->getConf('image.set');

        foreach(input::get('cart_shop') as $shopId => $cartShopChecked)
        {
            $pagedata['selectShop'][$shopId] = $cartShopChecked=='on' ? true : false;
        }
        $pagedata['selectAll'] = input::get('cart_all')=='on' ? true : false;

        $msg = view::make('topm/cart/cart_main.html', $pagedata)->render();

        return $this->splash('success',null,$msg,true);
    }

    public function ajaxBasicCart()
    {
        $cartData = app::get('topm')->rpcCall('trade.cart.getCartInfo', array('platform'=>'wap', 'user_id'=>userAuth::id()), 'buyer');

        $pagedata['aCart'] = $cartData['resultCartData'];

        $pagedata['totalCart'] = $cartData['totalCart'];

        $pagedata['defaultImageId'] = app::get('image')->getConf('image.set');

        foreach(input::get('cart_shop') as $shopId => $cartShopChecked)
        {
            $pagedata['selectShop'][$shopId] = $cartShopChecked=='on' ? true : false;
        }
        $pagedata['selectAll'] = input::get('cart_all')=='on' ? true : false;

        $msg = view::make('topm/cart/cart_main.html', $pagedata)->render();

        return $this->splash('success',null,$msg,true);
    }

    /**
     * @brief 删除购物车中数据
     *
     * @return
     */
    public function removeCart()
    {
        $postCartIdsData = input::get('cart_id');
        $tmpCartIds = array();
        foreach ($postCartIdsData as $cartId => $v)
        {
            if($v=='1')
            {
                $tmpCartIds['cart_id'][] = $cartId;
            }
        }
        $params['cart_id'] = implode(',',$tmpCartIds['cart_id']);

        try
        {
            $res = app::get('topm')->rpcCall('trade.cart.delete',$params);
            if( $res === false )
            {
                throw new Exception(app::get('topm')->_('删除失败'));
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
        return $this->splash('success',null,'删除成功',true);
    }

    /**
     * @brief 计算购物车金额
     *
     * @return
     */
    public function total()
    {
        $postData = input::get();
        if($postData['current_shop_id'])
        {
            $current_shop_id = $postData['current_shop_id'];
            unset($postData['current_shop_id']);
        }

        $params['user_id'] = userAuth::id();
        $params['addr_id'] = $postData['addr_id'];
        $params['fields'] = 'area';
        $addr = app::get('topm')->rpcCall('user.address.info',$params,'buyer');
        list($regions,$region_id) = explode(':', $addr['area']);

        $cartFilter['mode'] = $postData['mode'] ? $postData['mode'] :'cart';
        $cartFilter['needInvalid'] = $postData['checkout'] ? false : true;
        $cartFilter['platform'] = 'wap';
        $cartFilter['user_id'] = userAuth::id();
        $cartInfo = app::get('topm')->rpcCall('trade.cart.getCartInfo', $cartFilter,'buyer');

        $allPayment = 0;
        $objMath = kernel::single('ectools_math');

        foreach ($cartInfo['resultCartData'] as $shop_id => $tval) {
            $totalParams = array(
                'discount_fee' => $tval['cartCount']['total_discount'],
                'total_fee' => $tval['cartCount']['total_fee'],
                'total_weight' => $tval['cartCount']['total_weight'],
                'shop_id' => $tval['shop_id'],
                'template_id' => $postData['shipping'][$tval['shop_id']]['template_id'],
                'region_id' => str_replace('/', ',', $region_id),
                'usedCartPromotionWeight' => $tval['usedCartPromotionWeight'],
            );
            $totalInfo = app::get('topm')->rpcCall('trade.price.total',$totalParams,'buyer');
            $trade_data['allPayment'] = $objMath->number_plus(array($trade_data['allPayment'] ,$totalInfo['payment']));
            if($current_shop_id && $shop_id != $current_shop_id)
            {
                continue;
            }

            $trade_data['shop'][$shop_id]['payment'] = $totalInfo['payment'];
            $trade_data['shop'][$shop_id]['total_fee'] = $totalInfo['total_fee'];
            $trade_data['shop'][$shop_id]['discount_fee'] = $totalInfo['discount_fee'];
            $trade_data['shop'][$shop_id]['obtain_point_fee'] = $totalInfo['obtain_point_fee'];
            $trade_data['shop'][$shop_id]['post_fee'] = $totalInfo['post_fee'];
            $trade_data['shop'][$shop_id]['totalWeight'] += $tval['cartCount']['total_weight'];
        }
        return response::json($trade_data);exit;
    }

    public function checkout()
    {
        $postData =utils::_filter_input(input::get());
        $cartFilter['mode'] = $postData['mode'] ? $postData['mode'] :'cart';
        $pagedata['mode'] = $postData['mode'];

        try
        {
            /*获取收货地址 start*/
            if(isset($postData['addr_id']) && $postData['addr_id'])
            {
                $params['user_id'] = userAuth::id();
                $params['addr_id'] = $postData['addr_id'];
                $userDefAddr = app::get('topm')->rpcCall('user.address.info',$params);
            }
            else
            {
                $params['user_id'] = userAuth::id();
                $params['def_addr'] = 1;
                $userDefAddr = app::get('topm')->rpcCall('user.address.info',$params);
                if(!$userDefAddr)
                {
                    $userAddr= app::get('topm')->rpcCall('user.address.count',array('user_id'=>$params['user_id']));
                    $pagedata['nowcount'] = $userAddr['nowcount'];
                }
            }
            $pagedata['def_addr'] = $userDefAddr;
            /*获取收货地址 end*/

            if(isset($postData['pay_type']))
            {
                $pagedata['payType'] = array('pay_type'=>$postData['pay_type'],'name'=>$this->payType[$postData['pay_type']]);
            }

            //print_r($pagedata); exit;
            // 商品信息
            $cartFilter['needInvalid'] = false;
            $cartFilter['platform'] = 'wap';
            $cartFilter['user_id'] = userAuth::id();
            $cartInfo = app::get('topm')->rpcCall('trade.cart.getCartInfo', $cartFilter,'buyer');
            if(!$cartInfo)
            {
                return $this->splash('error', null, app::get('topm')->_('没有勾选购买商品或者商品数据已经失效'));
            }

            $isSelfShop = true;
            foreach($cartInfo['resultCartData'] as $key=>$val)
            {
                if($val['shop_type'] != "self")
                {
                    $isSelfShop = false;
                }
                else
                {
                    $isSelfShopArr[] = $val['shop_id'];
                }
            }
            $pagedata['isSelfShop'] = $isSelfShop;
            //echo "<pre>"; print_r($cartInfo);print_r($pagedata); exit;
            $pagedata['cartInfo'] = $cartInfo;

            //用户验证购物车数据是否发生变化
            $md5CartInfo = md5(serialize(utils::array_ksort_recursive(app::get('topm')->rpcCall('trade.cart.getBasicCartInfo', $cartFilter, 'buyer'), SORT_STRING)));
            $pagedata['md5_cart_info'] = $md5CartInfo;

            $ifOpenZiti = app::get('syslogistics')->getConf('syslogistics.ziti.open');
            if($isSelfShop && $ifOpenZiti == 'true' && $pagedata['def_addr'])
            {
                $area = explode(':',$pagedata['def_addr']['area']);
                $area = implode(',',explode('/',$area[1]));
                $zitiData = app::get('topm')->rpcCall('logistics.ziti.list',array('area_id'=>$area));
                $pagedata['zitiDataList'] = $zitiData;
            }

            $shop_ids = array_keys($pagedata['cartInfo']['resultCartData']);
            if( $isSelfShop )
            {
                $pagedata['dtyList'] = $this->__getDtyList($shop_ids,$isSelfShopArr,$zitiData);
            }
            else
            {
                $pagedata['dtyList'] = $this->__getDtyList($shop_ids,$isSelfShop);
            }

            // 优惠券列表
            foreach ($pagedata['cartInfo']['resultCartData'] as &$v)
            {
                $nocoupon = array('0'=>array('coupon_name'=>'不使用优惠券', 'coupon_code'=>'-1'));
                $validcoupon = $this->getCoupons($v['shop_id']);
                $v['couponList'] = array_merge($nocoupon, $validcoupon);
            }

            // 刷新结算页则失效前面选则的优惠券
            foreach($shop_ids as $sid)
            {
                $apiParams = array(
                    'coupon_code' => '-1',
                    'shop_id' => $sid,
                );
                app::get('topm')->rpcCall('trade.cart.cartCouponCancel', $apiParams, 'buyer');
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error', null, $msg);
        }

        return $this->page('topm/cart/checkout/index.html', $pagedata);
    }

    /**
     * @brief 获取收货地址列表
     *
     * @return  html
     */
    public function getAddrList()
    {
        $selectAddrId = input::get('selected');
        $ifedit = input::get('ifedit',false);

        $userId = userAuth::id();
        $userAddrList = app::get('topm')->rpcCall('user.address.list',array('user_id'=>$userId));
        $count = $userAddrList['count'];
        $userAddrList = $userAddrList['list'];
        foreach ($userAddrList as &$addr) {
            list($regions,$region_id) = explode(':', $addr['area']);
            $addr['region_id'] = str_replace('/', ',', $region_id);
            if($addr['def_addr'])
            {
                $userDefAddr = $addr;
            }
        }
        if(!$userAddrList)
        {
            return $this->editAddr();
        }
        $pagedata['userAddrList'] = $userAddrList;
        $pagedata['userDefAddr'] = $userDefAddr;
        $pagedata['selectedAddr'] = $selectAddrId;
        if($ifedit)
        {
            return $this->page('topm/cart/checkout/addredit.html', $pagedata);
        }
        else{
            return $this->page('topm/cart/checkout/addrlist.html', $pagedata);
        }
    }

    /**
     * @brief 修改收货地址
     *
     * @return
     */
    public function editAddr()
    {
        $selectAddrId = input::get('addr_id');
        $userId = userAuth::id();
        $addrInfo = app::get('topm')->rpcCall('user.address.info',array('addr_id'=>$selectAddrId,'user_id'=>$userId));
        list($regions,$region_id) = explode(':', $addrInfo['area']);
        $addrInfo['area'] = $regions;
        $addrInfo['region_id'] = str_replace('/', ',', $region_id);

        $pagedata['addrInfo'] = $addrInfo;
        $pagedata['addrdetail'] = $addrInfo['area'].'/'.$addrInfo['addr'];

        return $this->page('topm/cart/checkout/edit.html', $pagedata);
    }

    /**
     * @brief 购物车结算页
     *
     * @return
     */
    public function saveAddress()
    {
        $userId = userAuth::id();
        $postData = input::get();

        $postData['area'] = rtrim(input::get()['area'][0],',');

        $postData['user_id'] = $userId;
        $area = app::get('topm')->rpcCall('logistics.area',array('area'=>$postData['area']));

        if($area)
        {
            $areaId =  str_replace(",","/", $postData['area']);
            $postData['area'] = $area . ':' . $areaId;
        }
        else
        {
            $msg = app::get('topm')->_('地区不存在!');
            return $this->splash('error',null,$msg);
        }
        try
        {
            app::get('topm')->rpcCall('user.address.add',$postData);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();

            return $this->splash('error',null,$msg);
        }

        $url = url::action('topm_ctl_cart@getAddrList');
        return $this->splash('success',$url,$msg);
    }

    public function delAddr()
    {
        $postData = array(
            'addr_id' =>input::get('addr_id'),
            'user_id' => userAuth::id(),
        );

        try
        {
            app::get('topm')->rpcCall('user.address.del',$postData);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();

            return $this->splash('error',null,$msg);
        }
        $url = url::action('topm_ctl_cart@getAddrList', array('ifedit'=>true));
        $msg = app::get('topm')->_('删除成功');
        return $this->splash('success',$url,$msg);

    }

    public function getCoupons($shop_id)
    {
        // 默认取100个优惠券，用作一页显示，一般达不到这个数量一个店铺
        $params = array(
            'page_no' => 0,
            'page_size' => 100,
            'fields' => '*',
            'user_id' => userAuth::id(),
            'shop_id' => intval($shop_id),
            'is_valid' => 1,
            'platform' => 'wap',
        );
        $couponListData = app::get('topm')->rpcCall('user.coupon.list', $params, 'buyer');
        $couponList = $couponListData['coupons'];

        return $couponList;
    }

    public function useCoupon()
    {
        try
        {
            $mode = input::get('mode');
            $buyMode = $mode ? $mode :'cart';
            $apiParams = array(
                'coupon_code' => input::get('coupon_code'),
                'mode' => $buyMode,
                'platform' => 'wap',
            );
            if( app::get('topm')->rpcCall('promotion.coupon.use', $apiParams,'buyer') )
            {
                $msg = app::get('topm')->_('使用优惠券成功！');
                return $this->splash('success', null, $msg, true);
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
    }

    public function cancelCoupon()
    {
        try
        {
            $apiParams = array(
                'coupon_code' => input::get('coupon_code'),
                'shop_id' => input::get('shop_id'),
            );
            if( app::get('topm')->rpcCall('trade.cart.cartCouponCancel', $apiParams,'buyer') )
            {
                $msg = app::get('topm')->_('取消优惠券成功！');
                return $this->splash('success', null, $msg, true);
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
    }

    public function getPayTypeList()
    {
        $data = input::get('selected');
        $pagedata['payType'] = array(
            'pay_type' => $data,
            'name' => $this->payType[$data],
        );
        $pagedata['addr_id'] = input::get('addr_id');
        $pagedata['isSelfShop'] = input::get('s');
        $pagedata['mode'] = input::get('mode');
        return $this->page('topm/cart/checkout/paylist.html', $pagedata);
    }

    private function __getDtyList($shop_ids,$isSelfShop=null,$zitiData)
    {
        $tmpParams = array(
            'shop_id' => implode(',',$shop_ids),
            'status' => 'on',
            'fields' => 'shop_id,name,template_id',
        );
        $dtytmpls = app::get('topm')->rpcCall('logistics.dlytmpl.get.list',$tmpParams,'buyer');
        $dtytmplsBykey = array();
        foreach ($dtytmpls['data'] as $k => $tdy) {
            $dtytmplsBykey[$tdy['shop_id']][] = $tdy;
        }

        $ifOpenZiti = app::get('syslogistics')->getConf('syslogistics.ziti.open');
        if( $isSelfShop )
        {
            foreach($isSelfShop as $shopid)
            {
                if(!$dtytmplsBykey[$shopid])
                {
                    $dtytmplsBykey[$shopid][] = array(
                        'template_id' => -1,
                        'name' => '--选择配送方式--',
                    );
                }

                if( $zitiData && $ifOpenZiti == 'true' )
                {
                    $dtytmplsBykey[$shopid][] = array(
                        'template_id' => 0,
                        'name' => '上门自提',
                    );
                }
            }
        }
        return $dtytmplsBykey;
    }
}

