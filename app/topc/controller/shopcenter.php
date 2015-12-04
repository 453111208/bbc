<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_shopcenter extends topc_controller
{

    public $limit = 20;

    public function __construct($app)
    {
        parent::__construct();
        $this->app = $app;
        $this->setLayoutFlag('shopcenter');

        if( !$this->__checkShop(input::get('shop_id')) )
        {
            $pagedata['shopid'] = input::get('shop_id');
            $this->page('topc/shop/error.html', $pagedata)->send();
        }
    }

    /**
     * 检查shopId是否存在
     *
     * @param int $shopId 店铺ID
     */
    private function __checkShop($shopId)
    {
        $shopId = intval($shopId);
        if($shopId)
        {
            $shopdata = app::get('topc')->rpcCall('shop.get',array('shop_id'=>$shopId));
            if( empty($shopdata) || $shopdata['status'] == "dead" )
            {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取店铺模板页面头部共用部分的数据
     *
     * @param int $shopId 店铺ID
     * @return array
     */
    private function __common($shopId)
    {
        $shopId = intval($shopId);

        //店铺信息
        $shopdata = app::get('topc')->rpcCall('shop.get',array('shop_id'=>$shopId));
        $commonData['shopdata'] = $shopdata;

        //店铺招牌背景色
        $commonData['background_image'] = shopWidgets::getWidgetsData('shopsign',$shopId);

        //店铺菜单
        $navData = shopWidgets::getWidgetsData('nav',$shopId);
        $commonData['navdata'] = $navData;

        //获取默认图片信息
        $commonData['defaultImageId']= app::get('image')->getConf('image.set');

        return $commonData;
    }

    //店铺首页
    public function index()
    {
        $shopId = input::get('shop_id');

        $pagedata = $this->__common($shopId);
        if( userAuth::check() )
        {
            $pagedata['nologin'] = 1;
        }

        //店铺自定义区域
        $params = shopWidgets::getWidgetsData('custom',$shopId);
        if($params)
        {
            $pagedata['params'] = $params['custom'];
        }

        //店铺商品
        $items = shopWidgets::getWidgetsData('showitems',$shopId,'0,1');
        if( $items )
        {
            $itemIds = array();
            foreach( $items as $row )
            {
                $itemIds = array_merge($itemIds,array_column($row,'item_id'));
            }

            $activityParams['item_id'] = implode(',',array_unique($itemIds));
            $activityParams['status'] = 'agree';
            $activityParams['end_time'] = 'bthan';
            $activityParams['start_time'] = 'sthan';
            $activityParams['fields'] = 'activity_id,item_id,activity_tag,price,activity_price';
            $activityItemList = app::get('topc')->rpcCall('promotion.activity.item.list',$activityParams);
            if( $activityItemList )
            {
                $activatyItem = array_bind_key($activityItemList['list'],'item_id');
                foreach( $items as &$row )
                {
                    foreach( $row as &$item )
                    {
                        if( $activatyItem[$item['item_id']] )
                        {
                            $item['activity_tag'] = $activatyItem[$item['item_id']]['activity_tag'];
                            $item['price'] = $activatyItem[$item['item_id']]['activity_price'];
                        }
                    }
                }
            }
        }

        $pagedata['items'] = $items;

        // 店铺优惠券信息,
        $params = array(
            'page_no' => 0,
            'page_size' => 10,
            'fields' => '*',
            'shop_id' => $shopId,
            'platform' => 'pc',
            'is_cansend' => 1,
        );
        $couponListData = app::get('topc')->rpcCall('promotion.coupon.list', $params, 'buyer');
        $pagedata['homeCouponList']= $couponListData['coupons'];

        $pagedata['file'] = "topc/shop/center.html";

        return $this->page('topc/shop/index.html', $pagedata);
    }

    public function search()
    {
        $shopId = input::get('shop_id');

        $pagedata = $this->__common($shopId);

        $objLibFilter = kernel::single('topc_item_filter');
        $params = $objLibFilter->decode(input::get());
        $params['use_platform'] = '0,1';

        if($params['shop_id'])
        {
            $pagedata['shopCat'] = $shopCat = app::get('topc')->rpcCall('shop.cat.get',array('shop_id'=>$params['shop_id']));
            //echo "<pre>"; print_r($shopCat); exit;
        }

        if($params['shop_cat_id'] && $shopCat[$params['shop_cat_id']] )
        {
            $params['shop_cat_id'] = array_keys($shopCat[$params['shop_cat_id']]['children']);
            $params['shop_cat_id'] = implode(',', $params['shop_cat_id']);
        }

        $searchParams = $params;
        $searchParams['page_no'] = ($params['pages'] && $params['pages'] <= 100) ? $params['pages'] : 1;
        $searchParams['page_size'] = $this->limit;
        if( !isset($params['orderBy']) )
        {
            $params['orderBy'] =  'sold_quantity desc';
        }
        $searchParams['orderBy'] = $params['orderBy'];
        $searchParams['fields'] = 'item_id,title,image_default_id,price';

        $itemsList = app::get('topc')->rpcCall('item.search',$searchParams);
        //检测是否有参加团购活动
        if($itemsList)
        {
            $itemsList['list'] = array_bind_key($itemsList['list'],'item_id');
            $itemIds = array_keys($itemsList['list']);
            $activityParams['item_id'] = implode(',',$itemIds);
            $activityParams['status'] = 'agree';
            $activityParams['end_time'] = 'bthan';
            $activityParams['start_time'] = 'sthan';
            $activityParams['fields'] = 'activity_id,item_id,activity_tag,price,activity_price';
            $activityItemList = app::get('topc')->rpcCall('promotion.activity.item.list',$activityParams);
            if($activityItemList['list'])
            {
                foreach($activityItemList['list'] as $key=>$value)
                {
                    $itemsList['list'][$value['item_id']]['activity'] = $value;
                    $itemsList['list'][$value['item_id']]['price'] = $value['activity_price'];
                }
            }
        }

        $items = $itemsList['list'];
        $count = $itemsList['total_found'];

        $pagedata['items'] = $items;
        $pagedata['activeFilter'] = $params;

        $tmpFilter = $params;
        unset($tmpFilter['pages']);
        $pagedata['filter'] = $objLibFilter->encode($tmpFilter);

        $current = $params['pages'] ? $params['pages'] : 1;
        if($count > 0 ) $total = ceil($count/$this->limit);
        $params['pages'] = time();
        $pagedata['pages'] = array(
            'link' => url::action('topc_ctl_shopcenter@search',$params),
            'current' => $current,
            'total' => $total,
            'token' => $params['pages'],
        );
        $pagedata['file'] = "topc/shop/search.html";
        return $this->page('topc/shop/index.html', $pagedata);
    }

    public function shopCouponList()
    {
        $shopId = input::get('shop_id');
        $pagedata = $this->__common($shopId);

       // 店铺优惠券信息,
        $params = array(
            'page_no' => 0,
            'page_size' => 100,
            'fields' => '*',
            'shop_id' => $shopId,
            'platform' => 'pc',
            'is_cansend' => 1,
        );
        $couponListData = app::get('topc')->rpcCall('promotion.coupon.list', $params, 'buyer');
        $pagedata['shopCouponList'] = $couponListData['coupons'];
        $pagedata['file'] = "topc/shop/shopCouponList.html";
        return $this->page('topc/shop/index.html', $pagedata);
    }

    public function getCouponResult()
    {
        $shopId = input::get('shop_id');
        $pagedata = $this->__common($shopId);
        $coupon_id = input::get('coupon_id');
        $pagedata['couponInfo'] = app::get('topc')->rpcCall('promotion.coupon.get', array('coupon_id'=>$coupon_id));
        $pagedata['file'] = "topc/shop/couponResult.html";
        return $this->page('topc/shop/index.html', $pagedata);
    }

    public function getCouponCode()
    {
        $apiData['shop_id'] = $shopId = input::get('shop_id');
        $pagedata = $this->__common($shopId);
        $user_id = userAuth::id();
        if(!$user_id)
        {
            $signinUrl =  url::action('topc_ctl_passport@signin');
            return $this->splash('success', $signinUrl, '', true);
        }
        $coupon_id = input::get('coupon_id');
        if(!$coupon_id)
        {
            return $this->splash('error', '', '领取优惠券参数错误', true);
        }
        try
        {
            $userInfo = app::get('topc')->rpcCall('user.get.info',array('user_id'=>$user_id),'buyer');
            $apiData = array(
                 'coupon_id' => $coupon_id,
                 'user_id' =>$user_id,
                 'shop_id' =>$shopId,
                 'grade_id' =>$userInfo['grade_id'],
            );
            if(app::get('topc')->rpcCall('user.coupon.getCode', $apiData))
            {
                $url = url::action('topc_ctl_shopcenter@getCouponResult', array('coupon_id'=>$coupon_id, 'shop_id'=>$shopId));
                return $this->splash('success', $url, '领取成功', true);
                // $pagedata['couponInfo'] = app::get('topc')->rpcCall('promotion.coupon.get', array('coupon_id'=>$coupon_id));
                // $pagedata['file'] = "topc/shop/couponResult.html";
                // return $this->page('topc/shop/index.html', $pagedata);
            }
            else
            {
                return $this->splash('error', '', '领取失败', true);
            }
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error', '', $msg, true);
        }
    }

}


