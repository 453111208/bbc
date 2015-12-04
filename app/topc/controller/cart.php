<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topc_ctl_cart extends topc_controller {

    public function __construct(&$app)
    {
        parent::__construct();
        // 检测是否登录
        if( !userAuth::check() )
        {
            redirect::action('topc_ctl_passport@signin')->send();exit;
        }
    }

    public function index()
    {
        $pagedata['defaultImageId'] = app::get('image')->getConf('image.set');

        $cartData = app::get('topc')->rpcCall('trade.cart.getCartInfo', array('platform'=>'pc','user_id'=>userAuth::id()), 'buyer');
        $pagedata['aCart'] = $cartData['resultCartData'];

        $pagedata['totalCart'] = $cartData['totalCart'];
        // 店铺可领取优惠券
        foreach ($pagedata['aCart'] as &$v) {
            $params = array(
                'page_no' => 0,
                'page_size' => 1,
                'fields' => '*',
                'shop_id' => $v['shop_id'],
                'platform' => 'pc',
                'is_cansend' => 1,
            );
            $couponListData = app::get('topc')->rpcCall('promotion.coupon.list', $params, 'buyer');
            if($couponListData['count']>0)
            {
                $v['hasCoupon'] = 1;
            }
        }
// echo "<pre>";print_r($pagedata);exit;
        return $this->page('topc/cart/index.html', $pagedata);
    }

    public function ajaxBasicCart()
    {
        $cartData = app::get('topc')->rpcCall('trade.cart.getCartInfo', array('platform'=>'pc','user_id'=>userAuth::id()), 'buyer');

        $pagedata['aCart'] = $cartData['resultCartData'];

        $pagedata['totalCart'] = $cartData['totalCart'];

        $pagedata['defaultImageId'] = app::get('image')->getConf('image.set');

        foreach(input::get('cart_shop') as $shopId => $cartShopChecked)
        {
            $pagedata['selectShop'][$shopId] = $cartShopChecked=='on' ? true : false;
        }
        $pagedata['selectAll'] = input::get('cart_all')=='on' ? true : false;

        $msg = view::make('topc/cart/cart_main.html', $pagedata)->render();

        return $this->splash('success',null,$msg,true);
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
                $data = app::get('topc')->rpcCall('trade.cart.update',$updateParams);
                if( $data === false )
                {
                    $msg = app::get('topc')->_('更新失败');
                    return $this->splash('error',null,$msg,true);
                }
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }

        $cartData = app::get('topc')->rpcCall('trade.cart.getCartInfo', array('platform'=>'pc', 'user_id'=>userAuth::id()), 'buyer');
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

        $msg = view::make('topc/cart/cart_main.html', $pagedata)->render();

        return $this->splash('success',null,$msg,true);
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
            $data = app::get('topc')->rpcCall('trade.cart.add', $params, 'buyer');
            if( $data === false )
            {
                $msg = app::get('topc')->_('加入购物车失败');
                return $this->splash('error',null,$msg,true);
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }

        if( $params['mode'] == 'fastbuy' )
        {
            $url = url::action('topc_ctl_cart@checkout',array('mode'=>'fastbuy') );
        }
        userAuth::syncCookieWithCartNumber(app::get('topc')->rpcCall('trade.cart.getCount', ['user_id' => userAuth::id()], 'buyer'));
        return $this->splash('success',$url,$msg,true);
    }

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
        $params['user_id'] = userAuth::id();

        try
        {
            $res = app::get('topc')->rpcCall('trade.cart.delete',$params);
            if( $res === false )
            {
                throw new Exception(app::get('topc')->_('删除失败'));
            }

        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
        userAuth::syncCookieWithCartNumber(app::get('topc')->rpcCall('trade.cart.getCount', ['user_id' => userAuth::id()], 'buyer'));
        $url = url::action('topc_ctl_cart@index');
        return $this->splash('success',$url,'删除成功',true);
    }

    public function saveAddress()
    {
        $userId = userAuth::id();
        $postData = input::get();

        $postData['area'] = rtrim(input::get()['area'][0],',');

        $postData['user_id'] = $userId;
        $area = app::get('topc')->rpcCall('logistics.area',array('area'=>$postData['area']));

        if($area)
        {
            $areaId =  str_replace(",","/", $postData['area']);
            $postData['area'] = $area . ':' . $areaId;
        }
        else
        {
            $msg = app::get('topc')->_('地区不存在!');
            return $this->splash('error',null,$msg);
        }
        try
        {
            app::get('topc')->rpcCall('user.address.add',$postData);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();

            return $this->splash('error',null,$msg);
        }

        /*获取收货地址 start*/
        $params['user_id'] = userAuth::id();
        $userAddrList = app::get('topc')->rpcCall('user.address.list',$params);
        $userAddrList = $userAddrList['list'];
        foreach ($userAddrList as &$addr) {
            list($regions,$region_id) = explode(':', $addr['area']);
            $addr['region_id'] = str_replace('/', ',', $region_id);
        }
        $pagedata['userAddrList'] = $userAddrList;
        $params['def_addr'] = 1;
        $userDefAddr= app::get('topc')->rpcCall('user.address.info',$params);
        $pagedata['userDefAddr'] = $userDefAddr;
        return view::make('topc/checkout/address/addr_edit.html', $pagedata);
        /*收货地址 end*/
    }

    public function addr_dialog()
    {
        return view::make('topc/checkout/address/addr_dialog.html');
    }

    public function checkout()
    {
        $postData =utils::_filter_input(input::get());
        $cartFilter['mode'] = $postData['mode'] ? $postData['mode'] :'cart';
        $pagedata['mode'] = $postData['mode'];

        /*获取收货地址 start*/
        $params['user_id'] = userAuth::id();
        $userAddrList = app::get('topc')->rpcCall('user.address.list',$params);
        $userAddrList = $userAddrList['list'];
        foreach ($userAddrList as &$addr) {
            list($regions,$region_id) = explode(':', $addr['area']);
            $addr['region_id'] = str_replace('/', ',', $region_id);
        }
        $pagedata['userAddrList'] = $userAddrList;
        $params['def_addr'] = 1;
        $userDefAddr= app::get('topc')->rpcCall('user.address.info',$params);
        $pagedata['userDefAddr'] = $userDefAddr;
        /*收货地址 end*/

        // 商品信息
        $cartFilter['needInvalid'] = false;
        $cartFilter['platform'] = 'pc';
        $cartFilter['user_id'] = userAuth::id();
        $cartInfo = app::get('topc')->rpcCall('trade.cart.getCartInfo', $cartFilter,'buyer');
        if(!$cartInfo)
        {
            return $this->splash('error', null, app::get('topc')->_('没有勾选购买商品或者商品数据已经失效'));
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
        $pagedata['cartInfo'] = $cartInfo;

        //用户验证购物车数据是否发生变化
        $md5CartInfo = md5(serialize(utils::array_ksort_recursive(app::get('topc')->rpcCall('trade.cart.getBasicCartInfo', $cartFilter, 'buyer'), SORT_STRING)));
        $pagedata['md5_cart_info'] = $md5CartInfo;

        $shop_ids = array_keys($pagedata['cartInfo']['resultCartData']);
        if( $isSelfShop )
        {
            $pagedata['dtyList'] = $this->__getDtyList($shop_ids,$isSelfShopArr);
        }
        else
        {
            $pagedata['dtyList'] = $this->__getDtyList($shop_ids,$isSelfShop);
        }

        // 刷新结算页则失效前面选则的优惠券
        foreach($shop_ids as $sid)
        {
            $apiParams = array(
                'coupon_code' => '-1',
                'shop_id' => $sid,
            );
            app::get('topc')->rpcCall('trade.cart.cartCouponCancel', $apiParams, 'buyer');
        }

        return $this->page('topc/checkout/index.html', $pagedata);
    }

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
        $addr = app::get('topc')->rpcCall('user.address.info',$params,'buyer');
        list($regions,$region_id) = explode(':', $addr['area']);

        $cartFilter['needInvalid'] = $postData['checkout'] ? false : true;
        $cartFilter['platform'] = 'pc';
        $cartFilter['user_id'] = userAuth::id();
        $cartFilter['mode'] = $postData['mode'] ? $postData['mode'] :'cart';
        $cartInfo = app::get('topc')->rpcCall('trade.cart.getCartInfo', $cartFilter, 'buyer');

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
            $totalInfo = app::get('topc')->rpcCall('trade.price.total',$totalParams,'buyer');
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

    public function getCoupons()
    {
        $filter['pages'] = 1;
        $pageSize = 100;
        $params = array(
            'page_no' => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
            'fields' => '*',
            'user_id' => userAuth::id(),
            'shop_id' => intval(input::get('shop_id')),
            'is_valid' => 1,
            'platform' => 'pc',
        );
        $couponListData = app::get('topc')->rpcCall('user.coupon.list', $params, 'buyer');
        $pagedata['couponList'] = $couponListData['coupons'];
        // $pagedata['count'] = $couponListData['count'];

        return  view::make('topc/checkout/coupons.html', $pagedata)->render();
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
                'platform' => 'pc',
            );
            if( app::get('topc')->rpcCall('promotion.coupon.use', $apiParams,'buyer') )
            {
                $msg = app::get('topc')->_('使用优惠券成功！');
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
            if( app::get('topc')->rpcCall('trade.cart.cartCouponCancel', $apiParams,'buyer') )
            {
                $msg = app::get('topc')->_('取消优惠券成功！');
                return $this->splash('success', null, $msg, true);
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }

    }

    private function __getDtyList($shop_ids,$isSelfShop=null)
    {
        $tmpParams = array(
            'shop_id' => implode(',',$shop_ids),
            'status' => 'on',
            'fields' => 'shop_id,name,template_id',
        );
        $dtytmpls = app::get('topc')->rpcCall('logistics.dlytmpl.get.list',$tmpParams,'buyer');
        $dtytmplsBykey = array();
        foreach ($dtytmpls['data'] as $k => $tdy) {
            $dtytmplsBykey[$tdy['shop_id']][] = $tdy;
        }

        $ifOpenZiti = app::get('syslogistics')->getConf('syslogistics.ziti.open');
        if( $isSelfShop && $ifOpenZiti == 'true')
        {
            foreach($isSelfShop as $shopid)
            {
                $dtytmplsBykey[$shopid][] = array(
                    'template_id' => 0,
                    'name' => '上门自提',
                );
            }
        }

        foreach($dtytmplsBykey as $key=>$val)
        {
            $arr = array(
                'template_id' => -1,
                'name' => '--选择配送方式--',
            );
             array_unshift($dtytmplsBykey[$key],$arr);
        }
        return $dtytmplsBykey;
    }

    public function getZitiList()
    {
        $postData = input::get();
        $params['user_id'] = userAuth::id();
        $params['addr_id'] = $postData['addr_id'];
        $params['fields'] = "area";
        $addrInfo= app::get('topc')->rpcCall('user.address.info',$params);
        $area = explode(':',$addrInfo['area']);
        $area = implode(',',explode('/',$area[1]));
        $pagedata['data'] = app::get('topc')->rpcCall('logistics.ziti.list',array('area_id'=>$area));
        $pagedata['ziti_id'] = $postData['ziti_id'];
        return  view::make('topc/checkout/ziti.html', $pagedata)->render();
    }
}
