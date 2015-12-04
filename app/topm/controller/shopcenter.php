<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topm_ctl_shopcenter extends topm_controller
{

    public $limit = 10;
    public $maxPages = 100;

    public function __construct($app)
    {
        parent::__construct();
        $this->app = $app;
        $this->setLayoutFlag('shopcenter');

        if( !$this->__checkShop(input::get('shop_id')) )
        {
            $pagedata['shopid'] = input::get('shop_id');
            $this->page('topm/shop/error.html', $pagedata)->send();
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
        $shopdata = app::get('topm')->rpcCall('shop.get',array('shop_id'=>$shopId));
        if( empty($shopdata) || $shopdata['status'] == "dead" )
        {
            return false;
        }
        return true;
    }

    //排序
    public function array_sort($arr,$keys,$type='asc')
    {
        $keysvalue = $new_array = array();
        foreach ($arr as $k=>$v)
        {
            $keysvalue[$k] = $v[$keys];
        }
        if($type == 'asc')
        {
            asort($keysvalue);
        }
        else
        {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k=>$v)
        {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
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
        $shopdata = app::get('topm')->rpcCall('shop.get',array('shop_id'=>$shopId));
        $commonData['shopdata'] = $shopdata;

        //店铺招牌背景色
        $wapslider = shopWidgets::getWapInfo('waplogo',$shopId);
        //echo '<pre>';print_r($wapslider);exit();
        $commonData['logo_image'] = $wapslider[0]['params'];
        //$commonData['background_image'] = shopWidgets::getWidgetsData('shopsign',$shopId);

        //店铺论播广告
        $wapslider = shopWidgets::getWapInfo('wapslider',$shopId);
        $commonData['slider'] = $wapslider[0]['params'];
        //店铺菜单
        $navData = shopWidgets::getWidgetsData('nav',$shopId);
        $commonData['navdata'] = $navData;
        //标签展示
        $itemList = shopWidgets::getWapInfo('waptags',$shopId);
        $commonData['itemInfo'] = $this->__getItemInfo($itemList);

        //获取默认图片信息
        $commonData['defaultImageId']= app::get('image')->getConf('image.set');

        return $commonData;
    }

    //获取标签
    private function __getItemInfo($data)
    {
        $sort = unserialize(app::get('topshop')->getConf('wap_decorate.tagSort'));
        foreach ($data as $key => $value)
        {
            if($value['params']['isstart'])
            {
                $itemData[$value['widgets_id']] = $value;
                $itemData[$value['widgets_id']]['order_sort'] = $sort[$value['widgets_id']]['order_sort'];
            }
        }
        $items = $this->array_sort($itemData,'order_sort');

        return $items;
    }

    //获取商品
    private function __getShowItems($data)
    {
        $sort = unserialize(app::get('topshop')->getConf('wap_decorate.showItemSort'));
        foreach ($data as $key => $value)
        {
            if($value['params']['isstart'])
            {
                $itemData[$value['widgets_id']] = $value;
                $params=array('shop_id'=>$value['shop_id'],'use_platform'=>'0');
                $params['orderBy'] = 'modified_time desc';
                $params['size'] = $value['params']['itemlimit'];
                $item_id = '';
                foreach ($value['params']['item_id'] as $k => $v)
                {
                    $item_id .= $v.',';
                }
                $params['item_id'] = rtrim($item_id, ",");
                $itemsList = $this->__search($params);
                $itemData[$value['widgets_id']]['params']['itemlist'] = $itemsList;
                $itemData[$value['widgets_id']]['order_sort'] = $sort[$value['widgets_id']]['order_sort'];
            }

        }
        //echo '<pre>';print_r($itemData);exit();
        $items = $this->array_sort($itemData,'order_sort');
        return $items;
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
        //店铺分类查询
         if($shopId)
         {
             $pagedata['shopCat'] = $shopCat = app::get('topm')->rpcCall('shop.cat.get',array('shop_id'=>$shopId));
         }
        $pagedata['shopDsrData'] = $this->__getShopDsr($shopId);
        //店铺商品展示
        $showItems = shopWidgets::getWapInfo('wapshowitems',$shopId);

        $pagedata['showitems'] = $this->__getShowItems($showItems);
        //店铺广告图片展示
        $imageSlider = shopWidgets::getWapInfo('wapimageslider',$shopId);
        $pagedata['imageSlider'] = $imageSlider[0]['params'];

       // 店铺优惠券信息,
        $params = array(
            'page_no' => 0,
            'page_size' => 10,
            'fields' => '*',
            'shop_id' => $shopId,
            'platform' => 'wap',
            'is_cansend' => 1,
        );
        $couponListData = app::get('topm')->rpcCall('promotion.coupon.list', $params, 'buyer');
        $pagedata['homeCouponList'] = $couponListData['coupons'];
        $pagedata['now'] = time();
        $pagedata['title'] = $pagedata['shopdata']['shopname'];
        return $this->page('topm/shop/index.html', $pagedata);
    }

    private function __getShopDsr($shopId)
    {
        $params['shop_id'] = $shopId;
        $params['catDsrDiff'] = false;
        $dsrData = app::get('topm')->rpcCall('rate.dsr.get', $params);
        if( !$dsrData )
        {
            $countDsr['tally_dsr'] = sprintf('%.1f',5.0);
            $countDsr['attitude_dsr'] = sprintf('%.1f',5.0);
            $countDsr['delivery_speed_dsr'] = sprintf('%.1f',5.0);
        }
        else
        {
            $countDsr['tally_dsr'] = sprintf('%.1f',$dsrData['tally_dsr']);
            $countDsr['attitude_dsr'] = sprintf('%.1f',$dsrData['attitude_dsr']);
            $countDsr['delivery_speed_dsr'] = sprintf('%.1f',$dsrData['delivery_speed_dsr']);
        }
        $shopDsrData['countDsr'] = $countDsr;
        return $shopDsrData;
    }

    public function search()
    {
        $pagedata = $this->__getItems(input::get());

        //店铺信息
        $shopId = input::get('shop_id');
        $shopdata = app::get('topm')->rpcCall('shop.get',array('shop_id'=>$shopId));
        $pagedata['shopdata'] = $shopdata;

        $pagedata['title'] = $pagedata['shopdata']['shopname'];
        return $this->page('topm/shop/search.html', $pagedata);
    }

    private function __search($params)
    {
        $searchParams = $params;
        $searchParams['page_no'] = ($params['pages'] && $params['pages'] <= 100) ? $params['pages'] : 1;
        $searchParams['page_size'] = $params['size'] ? $params['size'] : $this->limit;
        if( !isset($params['orderBy']) )
        {
            $params['orderBy'] =  'sold_quantity desc';
        }
        $searchParams['orderBy'] = $params['orderBy'];
        $searchParams['fields'] = 'item_id,title,image_default_id,price';

        $itemsList = app::get('topm')->rpcCall('item.search',$searchParams);
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

        return $itemsList;
    }
    private function __getItems($data)
    {
        $objLibFilter = kernel::single('topm_item_filter');
        $params = $objLibFilter->decode($data);
        $params['use_platform'] = '0';

        if($params['shop_id'])
        {
             $pagedata['shopCat'] = $shopCat = app::get('topm')->rpcCall('shop.cat.get',array('shop_id'=>$params['shop_id']));
        }

        if($params['shop_cat_id'] && $shopCat[$params['shop_cat_id']] )
        {
            $params['shop_cat_id'] = array_keys($shopCat[$params['shop_cat_id']]['children']);
            $params['shop_cat_id'] = implode(',', $params['shop_cat_id']);
        }
        //标签获取
        if($params['widgets_id']&&$params['widgets_type'])
        {
            $tagInfo = shopWidgets::getWapInfo($params['widgets_type'],$data['shop_id'],$data['widgets_id']);
            foreach ($tagInfo[0]['params']['item_id'] as $key => $value)
            {
                $item_id .= $value.',';
            }
            $params['item_id'] = rtrim($item_id, ",");
        }

        $itemsList = $this->__search($params);
        $items = $itemsList['list'];
        $count = $itemsList['total_found'];

        $pagedata['items'] = $items;
        $pagedata['activeFilter'] = $params;

        $tmpFilter = $params;
        unset($tmpFilter['pages']);
        $pagedata['filter'] = $objLibFilter->encode($tmpFilter);
        $current = $params['pages'] ? $params['pages'] : 1;
        if($count > 0 ) $totalPage = ceil($count/$this->limit);

        $pagedata['pagers'] = array(
            'link' => url::action('topm_ctl_shopcenter@search',$params),
            'current' => $current,
            'total'=>($totalPage <= $this->maxPages) ? $totalPage : $this->maxPages,
        );

        if( userAuth::check() )
        {
            $pagedata['nologin'] = 1;
        }
        return $pagedata;
    }

    public function ajaxItemShow()
    {
        $pagedata = $this->__getItems(input::get());
        $data['html'] = view::make('topm/list/itemlist/itemshow.html',$pagedata)->render();
        $data['pagers'] = $pagedata['pagers'];
        $data['success'] = true;
        return response::json($data);exit;
    }

    public function shopCouponList()
    {
        $shopId = input::get('shop_id');
        $pagedata = $this->__common($shopId);

       // 店铺优惠券信息,
        $params = array(
            'page_no' => 0,
            'page_size' => 10,
            'fields' => '*',
            'shop_id' => $shopId,
            'platform' => 'wap',
            'is_cansend' => 1,
        );
        $couponListData = app::get('topm')->rpcCall('promotion.coupon.list', $params, 'buyer');
        $pagedata['shopCouponList'] = $couponListData['coupons'];

        return $this->page('topm/shop/shopCouponList.html', $pagedata);
    }

    public function getCouponResult()
    {
        $shopId = input::get('shop_id');
        $pagedata = $this->__common($shopId);
        $coupon_id = input::get('coupon_id');
        $pagedata['couponInfo'] = app::get('topm')->rpcCall('promotion.coupon.get', array('coupon_id'=>$coupon_id));
        // 获取会员等级名称
        $validGrade = explode(',', $pagedata['couponInfo']['valid_grade']);
        $gradeList = app::get('topm')->rpcCall('user.grade.list', array(), 'buyer');
        $gradeNameArr = array_bind_key($gradeList, 'grade_id');
        $validGradeNameArr = array();
        foreach($validGrade as $v)
        {
            $validGradeNameArr[] = $gradeNameArr[$v]['grade_name'];
        }
        $pagedata['couponInfo']['valid_grade_name'] = implode(',', $validGradeNameArr);
        return $this->page('topm/shop/couponResult.html', $pagedata);
    }

    public function getCouponCode()
    {
        $apiData['shop_id'] = $shopId = input::get('shop_id');
        $pagedata = $this->__common($shopId);
        $user_id = userAuth::id();
        if(!$user_id)
        {
            $loginUrl = url::action('topm_ctl_passport@signin');
            return $this->splash('success', $loginUrl, '请登录', true);
        }
        $coupon_id = input::get('coupon_id');
        if(!$coupon_id)
        {
            return $this->splash('error', '', '领取优惠券参数错误', true);
        }
        try
        {
            $userInfo = app::get('topm')->rpcCall('user.get.info',array('user_id'=>$user_id),'buyer');
            $apiData = array(
                 'coupon_id' => $coupon_id,
                 'user_id' =>$user_id,
                 'shop_id' =>$shopId,
                 'grade_id' =>$userInfo['grade_id'],
            );
            if(app::get('topm')->rpcCall('user.coupon.getCode', $apiData))
            {
                $url = url::action('topm_ctl_shopcenter@getCouponResult', array('coupon_id'=>$coupon_id, 'shop_id'=>$shopId));
                return $this->splash('success', $url, '领取失败', true);
                // $pagedata['couponInfo'] = app::get('topm')->rpcCall('promotion.coupon.get', array('coupon_id'=>$coupon_id));
                // $pagedata['file'] = "topm/shop/couponResult.html";
                // return $this->page('topm/shop/index.html', $pagedata);
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


