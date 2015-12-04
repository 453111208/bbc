<?php
class topshop_ctl_promotion_xydiscount extends topshop_controller {

    public function list_xydiscount()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('X件Y折管理');
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
        $xydiscountListData = app::get('topshop')->rpcCall('promotion.xydiscount.list', $params,'seller');
        $count = $xydiscountListData['total'];
        $pagedata['xydiscountList'] = $xydiscountListData['data'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topshop_ctl_promotion_xydiscount@list_xydiscount', $filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        $gradeList = app::get('topshop')->rpcCall('user.grade.list');
        // 组织会员等级的key,value的数组，方便取会员等级名称
        $gradeKeyValue = array_bind_key($gradeList, 'grade_id');

        // 增加列表中会员等级名称字段
        foreach($pagedata['xydiscountList'] as &$v)
        {
            $valid_grade = explode(',', $v['valid_grade']);

            $checkedGradeName = array();
            foreach($valid_grade as $gradeId)
            {
                $checkedGradeName[] = $gradeKeyValue[$gradeId]['grade_name'];
            }
            $v['valid_grade_name'] = implode(',', $checkedGradeName);
            $v['condition_value'] = $this->condition($v['condition_value']);
        }

        $pagedata['now'] = time();
        $pagedata['total'] = $count;

        return $this->page('topshop/promotion/xydiscount/index.html', $pagedata);
    }


    public function edit_xydiscount()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('添加/编辑X件Y折促销');

        $apiData['xydiscount_id'] = input::get('xydiscount_id');
        $apiData['xydiscount_itemList'] = true;
        if($apiData['xydiscount_id'])
        {
            $pagedata = app::get('topshop')->rpcCall('promotion.xydiscount.get', $apiData);

            $pagedata['valid_time'] = date('Y/m/d', $pagedata['start_time']) . '-' . date('Y/m/d', $pagedata['end_time']);
            if($pagedata['shop_id']!= $this->shopId)
            {
                return $this->splash('error','','您没有权限编辑此X件Y折促销',true);
            }
            $objMdlXydiscountItem = app::get('syspromotion')->model('xydiscount_item');
            $notEndItem = $objMdlXydiscountItem->getList('item_id', array('end_time|than'=>time() ) );
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
        $shopId = shopAuth::getShopId();
        $pagedata['shopCatList'] = app::get('topshop')->rpcCall('shop.authorize.cat',array('shop_id'=>$shopId));
        $pagedata['condition_value'] = $this->condition($pagedata['condition_value']);
        return $this->page('topshop/promotion/xydiscount/edit.html', $pagedata);
    }
    public function condition($condition)
    {
        $condList = explode(',',$condition);
        foreach ($condList as $key => $value)
        {
            $condList[$key] = explode('|',$value);
        }
        return $condList;
    }

    public function save_xydiscount()
    {
        $params = input::get();

        $apiData['xydiscount_id'] = $params['xydiscount_id'];
        $apiData['xydiscount_name'] = $params['xydiscount_name'];
        $apiData['limit_number'] = intval($params['limit_number']);
        $apiData['discount'] = intval($params['discount']);
        $apiData['join_limit'] = intval($params['join_limit']);
        $apiData['used_platform'] = intval($params['used_platform']);
        $apiData['free_postage'] = intval($params['free_postage']);

        if( !$params['xydiscount_name'] )
        {
            return $this->splash('error','','X件Y折促销名称不能为空!',true);
        }
        if( !is_array($params['limit_number']) && count($params['limit_number']) && !is_array($params['discount']) && count($params['discount']) )
        {
            return $this->splash('error','','X件Y折条件至少添加一个!',true);
        }
        //新添加的字段condition_value
        $limitNumber = $params['limit_number'];
        $discount = $params['discount'];
        $joinxydiscount = array();
        foreach($limitNumber as $k=>$v)
        {
            $joinxydiscount[] = $v.'|'.$discount[$k];
        }
        $apiData['condition_value'] = implode(',', $joinxydiscount);

        $apiData['shop_id'] = $this->shopId;
        $timeArray = explode('-', $params['valid_time']);
        $apiData['start_time']  = strtotime($timeArray[0]. ' 00:00:00');
        $apiData['end_time'] = strtotime($timeArray[1]. ' 23:59:59');
        $apiData['valid_grade'] = implode(',', $params['grade']);

        $apiData['xydiscount_rel_itemids'] = implode(',', array_unique($params['item_id'])); // X件Y折关联的商品id,格式 商品id  '23,99,103',以逗号分割
        try
        {
            if($params['xydiscount_id'])
            {
                // 修改X件Y折促销
                $result = app::get('topshop')->rpcCall('promotion.xydiscount.update', $apiData);
            }
            else
            {
                // 新添X件Y折促销
                $result = app::get('topshop')->rpcCall('promotion.xydiscount.add', $apiData);
            }
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            if($params['xydiscount_id'])
            {
                $url = url::action('topshop_ctl_promotion_xydiscount@edit_xydiscount', array('xydiscount_id'=>$params['xydiscount_id']));
            }
            else{
                $url = url::action('topshop_ctl_promotion_xydiscount@list_xydiscount');
            }
            return $this->splash('error',$url,$msg,true);
        }
        $url = url::action('topshop_ctl_promotion_xydiscount@list_xydiscount');
        $msg = app::get('topshop')->_('保存X件Y折促销成功');
        return $this->splash('success',$url,$msg,true);
    }

    public function delete_xydiscount()
    {
        $apiData['shop_id'] = $this->shopId;
        $apiData['xydiscount_id'] = input::get('xydiscount_id');
        $url = url::action('topshop_ctl_promotion_xydiscount@list_xydiscount');
        try
        {
            app::get('topshop')->rpcCall('promotion.xydiscount.delete', $apiData);
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error', $url, $msg, true);
        }
        $msg = app::get('topshop')->_('删除X件Y折促销成功');
        return $this->splash('success', $url, $msg, true);
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
        $xydiscountId = input::get('xydiscountId');
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

        $searchParams['fields'] = 'item_id,title,image_default_id,price,brand_id';
        $itemsList = app::get('topshop')->rpcCall('item.search',$searchParams);
        $pagedata['itemsList'] = $itemsList['list'];
        $pagedata['image_default_id'] = app::get('image')->getConf('image.set');
        if($xydiscountId)
        {
            $objMdlXydiscountItem = app::get('syspromotion')->model('xydiscount_item');
            $notEndItem = $objMdlXydiscountItem->getList('item_id', array('end_time|than'=>time() ) );

            $pagedata['notEndItem'] = array_column($notEndItem, 'item_id');
        }
        else
        {
             $pagedata['notEndItem'] = array();
        }
        return response::json($pagedata);
    }


}
