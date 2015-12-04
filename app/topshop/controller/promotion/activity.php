<?php
class topshop_ctl_promotion_activity extends topshop_controller {

    // 我的活动报名列表
    public function registered_activity()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('活动报名');
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = 10;
        $params = array(
            'page_no' => intval($filter['pages']),
            'page_size' => $pageSize,
            'fields' =>'*',
            'shop_id' => $this->shopId,
        );
        $activityRegisterListData = app::get('topshop')->rpcCall('promotion.activity.register.list', $params, 'seller');
        $count = $activityRegisterListData['count'];
        foreach ($activityRegisterListData['data'] as &$v)
        {
            $acparams = array(
                'activity_id' => $v['activity_id'],
                'fields' => '*',
            );
            $activityDetail = app::get('topshop')->rpcCall('promotion.activity.info', $acparams, 'seller');
            $v['activity_name'] = $activityDetail['activity_name'];
            $v['start_time'] = $activityDetail['start_time'];
            $v['end_time'] = $activityDetail['end_time'];
            $v['activity_tag'] = $activityDetail['activity_tag'];
        }

        $pagedata['activityList'] = $activityRegisterListData['data'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topshop_ctl_promotion_activity@registered_activity', $filter),
            'current'=>$current,
            'use_app' => 'topshop',
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        $pagedata['now'] = time();
        $pagedata['total'] = $count;

        return $this->page('topshop/promotion/activity/registered.html', $pagedata);
    }

    //活动列表
    public function activity_list()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('活动列表');
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = 10;
        $params = array(
            'page_no' => intval($filter['pages']),
            'page_size' => $pageSize,
            'order_by' => 'apply_end_time desc',
            'fields' =>'*',
        );
        $activityListData = app::get('topshop')->rpcCall('promotion.activity.list', $params, 'seller');

        foreach ($activityListData['data'] as $key => $value)
        {
            $data['activity_id'] = $value['activity_id'];
            $data['shop_id'] = $this->shopId;
            $registered_activity = app::get('topshop')->rpcCall('promotion.activity.register.list', $data, 'seller');
            if($registered_activity['data'])
            {
                $activityListData['data'][$key]['verify_status'] = $registered_activity['data'][0]['verify_status'];
            }
        }
        //echo '<pre>';print_r($activityListData);exit();
        //获取商家店铺信息(shop、shop_info、brand)
        $shopId = $this->shopId;
        $params = array(
            'shop_id' => $shopId,
            'fields' =>'cat.cat_name,cat.cat_id,brand.brand_name,brand.brand_id,info',
        );
        $shopdata = app::get('topshop')->rpcCall('shop.get.detail',$params);
        foreach ($shopdata['cat'] as $key => $value)
        {
            $catId[$key] = $value['cat_id'];
        }
        $shoptype = $shopdata['shop']['shop_type'];
        foreach ($activityListData['data'] as $key => $value)
        {
            if(array_intersect($catId,$value['limit_cat']) && in_array($shoptype,explode(',',$value['shoptype'])))
            {
                $activityListData['data'][$key]['isactivity']=1;
            }
            else
            {
                $activityListData['data'][$key]['isactivity']=0;
            }
        }
        //echo '<pre>';print_r($activityListData);exit();
        $count = $activityListData['count'];
        $pagedata['activityList'] = $activityListData['data'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topshop_ctl_promotion_activity@activity_list', $filter),
            'current'=>$current,
            'use_app' => 'topshop',
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        $pagedata['now'] = time();
        $pagedata['total'] = $count;
        //echo '<pre>';print_r($pagedata);exit();
        return $this->page('topshop/promotion/activity/list.html', $pagedata);
    }
    // 活动表名提交页面
    public function canregistered_apply()
    {
        $activityId = intval(input::get('activity_id'));

        // 获取活动规则信息
        $activityParams = array(
            'activity_id' => $activityId,
            'shop_id'=>$this->shopId,
            'fields' => '*',
        );

        $registered_activity = app::get('topshop')->rpcCall('promotion.activity.register.list', $activityParams, 'seller');

        $pagedata = app::get('topshop')->rpcCall('promotion.activity.info', $activityParams, 'seller');
        if($registered_activity)
        {
            $pagedata['registered_activity'] = $registered_activity['data'];
        }

        if($pagedata['activity_id']=='')
        {
            throw new \LogicException(app::get('topshop')->_('异常操作！'));
        }
        $pagedata['limit_cat'] = implode(',', $pagedata['limit_cat']);
        $pagedata['shoptype'] = implode(',', $pagedata['shoptype']);

        // 获取商家活动报名的商品信息
        $itemParams = array(
            'fields' => '*',
            'shop_id' => $this->shopId,
            'activity_id' => $activityId,
        );

        $registerItemList = app::get('topshop')->rpcCall('promotion.activity.item.list', $itemParams, 'seller');
        $pagedata['itemsList'] = $registerItemList['list'];

        // 去重已经参加的活动商品
        $notItems = array_column($pagedata['itemsList'], 'item_id');
        $pagedata['notEndItem'] =  json_encode($notItems,true);

        $pagedata['shopCatList'] = app::get('topshop')->rpcCall('shop.authorize.cat',array('shop_id'=>$this->shopId));

        return $this->page('topshop/promotion/activity/canregistered_apply.html', $pagedata);
    }

    public function canregistered_apply_save()
    {
        $params = input::get();

        $apiData['shop_id'] = $this->shopId;
        $apiData['activity_id'] = (int) $params['activity_id'];
        //判断重复提交
        $data['activity_id'] = (int) $params['activity_id'];
        $data['shop_id'] = $this->shopId;
        $registered_activity = app::get('topshop')->rpcCall('promotion.activity.register.list', $data, 'seller');
        if($registered_activity['data'])
        {
            $msg = '该活动不能已经报过名了，不可以重复报名！';
            return $this->splash('error',null,$msg);
        }

        $itemWithPrice = array();
        if(!$params['item_activity_price'])
        {
            $msg = '您还没有选择商品，请重新选择！';
            return $this->splash('error',null,$msg);
        }

        foreach ($params['item_activity_price'] as $itemId => $activityPrice)
        {
            $validator = validator::make(
                    [$activityPrice,$itemId],
                    ['required|numeric','required|numeric'],
                    ['请设置商品价格!|商品价格格式有误!','请设置活动商品!|请勿使用非法手段更改商品数据!']
            );
            if ($validator->fails())
            {
                $messages = $validator->messagesInfo();
                foreach( $messages as $error )
                {
                    return $this->splash('error',null,$error[0]);
                }
            }
            $itemWithPrice[] = $itemId.':'.$activityPrice;
        }
        $apiData['item_info'] = implode(';', $itemWithPrice);
        //echo '<pre>';print_r($apiData);exit();
        try
        {
            // 活动报名保存
            $result = app::get('topshop')->rpcCall('promotion.activity.register', $apiData, 'seller');
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            $url = url::action('topshop_ctl_promotion_activity@canregistered_apply', array('activity_id'=>$params['activity_id']));
            return $this->splash('error',$url,$msg,true);
        }
        $url = url::action('topshop_ctl_promotion_activity@activity_list');
        $msg = app::get('topshop')->_('申请活动保存成功');
        return $this->splash('success',$url,$msg,true);

    }

    // 历史报名活动列表
    public function historyregistered_activity()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('活动管理');
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = 10;
        $params = array(
            'page_no' => intval($filter['pages']),
            'page_size' => $pageSize,
            'shop_id' => $this->shopId,
            'valid_status'=>0,
            'fields' =>'*',
        );

        $activityRegisterListData = app::get('topshop')->rpcCall('promotion.activity.register.list', $params, 'seller');
        $count = $activityRegisterListData['count'];
        foreach ($activityRegisterListData['data'] as &$v)
        {
            $acparams = array(
                'activity_id' => $v['activity_id'],
                'fields' => '*',
            );
            $activityDetail = app::get('topshop')->rpcCall('promotion.activity.info', $acparams, 'seller');
            $v['activity_name'] = $activityDetail['activity_name'];
            $v['start_time'] = $activityDetail['start_time'];
            $v['end_time'] = $activityDetail['end_time'];
            $v['activity_tag'] = $activityDetail['activity_tag'];
        }

        $pagedata['activityList'] = $activityRegisterListData['data'];


        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topshop_ctl_promotion_activity@historyregistered_activity', $filter),
            'current'=>$current,
            'use_app' => 'topshop',
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        $pagedata['total'] = $count;

        return $this->page('topshop/promotion/activity/historyregistered.html', $pagedata);
    }
    //历史报名活动详情
    public function historyregistered_detail()
    {
        $activityId = intval(input::get('activity_id'));

        // 获取活动规则信息
        $activityParams = array(
            'activity_id' => $activityId,
            'fields' => '*',
        );
        $pagedata = app::get('topshop')->rpcCall('promotion.activity.info', $activityParams, 'seller');
        if($pagedata['activity_id']=='')
        {
            throw new \LogicException(app::get('topshop')->_('异常操作！'));
        }
        $pagedata['limit_cat'] = implode(',', $pagedata['limit_cat']);
        $pagedata['shoptype'] = implode(',', $pagedata['shoptype']);

        // 获取商家活动报名的商品信息
        $itemParams = array(
            'fields' => '*',
            'shop_id' => $this->shopId,
            'activity_id' => $activityId,
        );

        $registerItemList = app::get('topshop')->rpcCall('promotion.activity.item.list', $itemParams, 'seller');
        $pagedata['itemsList'] = $registerItemList['list'];
        //echo '<pre>';print_r($pagedata);exit();

        return $this->page('topshop/promotion/activity/historyregistered_detail.html', $pagedata);
    }

    // 不可报名活动详情
    public function noregistered_detail()
    {
        $params = array(
            'activity_id' => intval(input::get('activity_id')),
            'fields' => '*',
        );
        $pagedata = app::get('topshop')->rpcCall('promotion.activity.info', $params, 'seller');
        $pagedata['limit_cat'] = implode(',', $pagedata['limit_cat']);
        $pagedata['shoptype'] = implode(',', $pagedata['shoptype']);
        return $this->page('topshop/promotion/activity/noregistered_detail.html', $pagedata);
    }
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
        $activityId = input::get('activityId');
        if($brandId)
        {
            $searchParams = array(
                'shop_id' => $shopId,
                'cat_id' => $catId,
                'brand_id' => $brandId,
                'approve_status'=>'onsale',
                'search_keywords' =>$keywords,
                'page_size' => 1000,
            );
        }
        else
        {
            $searchParams = array(
                'shop_id' => $shopId,
                'cat_id' => $catId,
                'approve_status'=>'onsale',
                'search_keywords' =>$keywords,
                'page_size' => 1000,
            );
        }

        $searchParams['fields'] = 'item_id,title,image_default_id,price';
        $itemsList = app::get('topshop')->rpcCall('item.search',$searchParams);
        $pagedata['itemsList'] = $itemsList['list'];
        $pagedata['image_default_id'] = app::get('image')->getConf('image.set');
        if($activityId)
        {
            $objMdlActivityItem = app::get('syspromotion')->model('activity_item');
            $notEndItem = $objMdlActivityItem->getList('item_id', array('end_time|than'=>time() ,'activity_id'=>$activityId) );
            $pagedata['notEndItem'] = array_column($notEndItem, 'item_id');
        }
        else
        {
             $pagedata['notEndItem'] = array();
        }
        return response::json($pagedata);
    }
}


