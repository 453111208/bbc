<?php
class topshop_ctl_promotion_coupon extends topshop_controller {

    public function list_coupon()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('优惠券管理');
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = 10;
        $params = array(
            'page_no' => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
            'fields' =>'*',
            'shop_id'=> $this->shopId,
        );
        $couponListData = app::get('topshop')->rpcCall('promotion.coupon.list', $params,'seller');
        $count = $couponListData['count'];
        $pagedata['couponList'] = $couponListData['coupons'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topshop_ctl_promotion_coupon@list_coupon', $filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        $pagedata['now'] = time();
        $pagedata['total'] = $count;

        return $this->page('topshop/promotion/coupon/index.html', $pagedata);
    }

    public function edit_coupon()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('新添/编辑优惠券');
        $apiData['coupon_id'] = input::get('coupon_id');
        $apiData['coupon_itemList'] = true;
        if($apiData['coupon_id'])
        {
            $pagedata = app::get('topshop')->rpcCall('promotion.coupon.get', $apiData);
            $pagedata['valid_time'] = date('Y/m/d', $pagedata['canuse_start_time']) . '-' . date('Y/m/d', $pagedata['canuse_end_time']);
            $pagedata['cansend_time'] = date('Y/m/d', $pagedata['cansend_start_time']) . '-' . date('Y/m/d', $pagedata['cansend_end_time']);
            if($pagedata['shop_id']!=$this->shopId)
            {
                return $this->splash('error','','您没有权限编辑此优惠券',true);
            }
            $objMdlCouponItem = app::get('syspromotion')->model('coupon_item');
            $notEndItem = $objMdlCouponItem->getList('item_id', array('canuse_end_time|than'=>time() ,'coupon_id'=>$couponId) );
            $notItems = array_column($notEndItem, 'item_id');
            $pagedata['notEndItem'] =  json_encode($notItems,true);
        }

        $valid_grade = explode(',', $pagedata['valid_grade']);
        $pagedata['gradeList'] = app::get('topshop')->rpcCall('user.grade.list');
        foreach($pagedata['gradeList'] as &$v)
        {
            if( in_array($v['grade_id'], $valid_grade) )
            {
                $v['is_checked'] = true;
            }
        }
        // $pagedata['shopCatList'] = json_decode($this->getCatList(),true);
        $shopId = shopAuth::getShopId();
        $pagedata['shopCatList'] = app::get('topshop')->rpcCall('shop.authorize.cat',array('shop_id'=>$shopId));
        return $this->page('topshop/promotion/coupon/edit.html', $pagedata);
    }

    public function save_coupon()
    {
        $params = input::get();

        $apiData = $params;
        $apiData['shop_id'] = $this->shopId;
        // 可使用的有效期
        $canuseTimeArray = explode('-', $params['valid_time']);
        $apiData['canuse_start_time']  = strtotime($canuseTimeArray[0]. ' 00:00:00');
        $apiData['canuse_end_time'] = strtotime($canuseTimeArray[1]. ' 23:59:59');
        // 可以领取的时间段
        $cansendTimeArray = explode('-', $params['cansend_time']);
        $apiData['cansend_start_time']  = strtotime($cansendTimeArray[0]. ' 00:00:00');
        $apiData['cansend_end_time'] = strtotime($cansendTimeArray[1]. ' 23:59:59');
        // 可以使用的会员等级
        $apiData['valid_grade'] = implode(',', $params['grade']);
        $apiData['coupon_rel_itemids'] = implode(',',$params['item_id']); // 满减关联的商品id,格式 商品id  '23,99,103',以逗号分割

        try
        {
            if($params['coupon_id'])
            {
                // 修改优惠券
                $result = app::get('topshop')->rpcCall('promotion.coupon.update', $apiData);
            }
            else
            {
                // 新添优惠券
                $result = app::get('topshop')->rpcCall('promotion.coupon.add', $apiData);
            }
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            $url = url::action('topshop_ctl_promotion_coupon@edit_coupon', array('coupon_id'=>$params['coupon_id']));
            return $this->splash('error',$url,$msg,true);
        }
        $url = url::action('topshop_ctl_promotion_coupon@list_coupon');
        $msg = app::get('topshop')->_('保存优惠券成功');
        return $this->splash('success',$url,$msg,true);
    }

    public function delete_coupon()
    {
        $apiData['shop_id'] = $this->shopId;
        $apiData['coupon_id'] = input::get('coupon_id');
        $url = url::action('topshop_ctl_promotion_coupon@list_coupon');
        try
        {
            app::get('topshop')->rpcCall('promotion.coupon.delete', $apiData);
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error', $url, $msg, true);
        }
        $msg = app::get('topshop')->_('删除优惠券成功');
        return $this->splash('success', $url, $msg, true);
    }

    //根据商家id的获取商家所经营的所有类目
    // public function getCatList()
    // {
    //     $shopId = shopAuth::getShopId();
    //     $catInfo = app::get('topshop')->rpcCall('shop.authorize.cat',array('shop_id'=>$shopId));
    //     return response::json($catInfo);
    // }

    //根据商家id和3级分类id获取商家所经营的所有品牌
    public function getBrandList()
    {
        $shopId = $this->shopId;
        $catId = input::get('catId');
        $params = array(
            'shop_id'=>$shopId,
            'cat_id'=>$catId,
            'fields'=>'brand_id,brand_name,brand_url'
        );
        $brands = app::get('topshop')->rpcCall('category.get.cat.rel.brand',$params);
        return response::json($brands);
    }

    //根据商家类目id的获取商家所经营类目下的所有商品
    public function searchItem()
    {
        $shopId = $this->shopId;
        $catId = input::get('catId');
        $brandId = input::get('brandId');
        $keywords = input::get('searchname');
        $couponId = input::get('couponId');
        if($brandId)
        {
            $searchParams = array(
                'shop_id' => $shopId,
                'cat_id' => $catId,
                'brand_id' => $brandId,
                'search_keywords' =>$keywords,
                'page_size' => 1000,
            );
        }
        else
        {
            $searchParams = array(
                'shop_id' => $shopId,
                'cat_id' => $catId,
                'search_keywords' =>$keywords,
                'page_size' => 1000,
            );
        }

        $searchParams['fields'] = 'item_id,title,image_default_id,price';
        $itemsList = app::get('topshop')->rpcCall('item.search',$searchParams);
        $pagedata['itemsList'] = $itemsList['list'];
        $pagedata['image_default_id'] = app::get('image')->getConf('image.set');
        if($couponId)
        {
            $objMdlCouponItem = app::get('syspromotion')->model('coupon_item');
            $notEndItem = $objMdlCouponItem->getList('item_id', array('canuse_end_time|than'=>time() ,'coupon_id'=>$couponId) );
            $pagedata['notEndItem'] = array_column($notEndItem, 'item_id');
        }
        else
        {
             $pagedata['notEndItem'] = array();
        }
        return response::json($pagedata);
    }

}
