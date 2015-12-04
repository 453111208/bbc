<?php
class topshop_ctl_promotion_freepostage extends topshop_controller {

    public function list_freepostage()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('免邮管理');
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
        $freepostageListData = app::get('topshop')->rpcCall('promotion.freepostage.list', $params,'seller');
        $count = $freepostageListData['count'];
        $pagedata['freepostageList'] = $freepostageListData['freepostages'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topshop_ctl_promotion_freepostage@list_freepostage', $filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        $gradeList = app::get('topshop')->rpcCall('user.grade.list');
        // 组织会员等级的key,value的数组，方便取会员等级名称
        $gradeKeyValue = array_bind_key($gradeList, 'grade_id');

        // 增加列表中会员等级名称字段
        foreach($pagedata['freepostageList'] as &$v)
        {
            $valid_grade = explode(',', $v['valid_grade']);

            $checkedGradeName = array();
            foreach($valid_grade as $gradeId)
            {
                $checkedGradeName[] = $gradeKeyValue[$gradeId]['grade_name'];
            }
            $v['valid_grade_name'] = implode(',', $checkedGradeName);
        }

        $pagedata['now'] = time();
        $pagedata['total'] = $count;

        return $this->page('topshop/promotion/freepostage/index.html', $pagedata);
    }



    public function edit_freepostage()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('新添/编辑免邮');
        $apiData['freepostage_id'] = input::get('freepostage_id');
        $apiData['freepostage_itemList'] = true;
        if($apiData['freepostage_id'])
        {
            $pagedata = app::get('topshop')->rpcCall('promotion.freepostage.get', $apiData);
            $pagedata['valid_time'] = date('Y/m/d', $pagedata['start_time']) . '-' . date('Y/m/d', $pagedata['end_time']);
            if($pagedata['shop_id']!=$this->shopId)
            {
                return $this->splash('error','','您没有权限编辑此免邮',true);
            }
            $objMdlFreepostageItem = app::get('syspromotion')->model('freepostage_item');
            $notEndItem = $objMdlFreepostageItem->getList('item_id', array('end_time|than'=>time() ,'freepostage_id'=>$freepostageId) );
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
        return $this->page('topshop/promotion/freepostage/edit.html', $pagedata);
    }

    public function save_freepostage()
    {
        $params = input::get();

        $apiData = $params;
        $apiData['shop_id'] = $this->shopId;
        // 可使用的有效期
        $canuseTimeArray = explode('-', $params['valid_time']);
        $apiData['start_time']  = strtotime($canuseTimeArray[0]. ' 00:00:00');
        $apiData['end_time'] = strtotime($canuseTimeArray[1]. ' 23:59:59');
        // 可以使用的会员等级
        $apiData['valid_grade'] = implode(',', $params['grade']);
        $apiData['freepostage_rel_itemids'] = implode(',',$params['item_id']); // 满减关联的商品id,格式 商品id  '23,99,103',以逗号分割

        try
        {
            if($params['freepostage_id'])
            {
                // 修改免邮
                $result = app::get('topshop')->rpcCall('promotion.freepostage.update', $apiData);
            }
            else
            {
                // 新添免邮
                $result = app::get('topshop')->rpcCall('promotion.freepostage.add', $apiData);
            }
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            $url = url::action('topshop_ctl_promotion_freepostage@edit_freepostage', array('freepostage_id'=>$params['freepostage_id']));
            return $this->splash('error',$url,$msg,true);
        }
        $url = url::action('topshop_ctl_promotion_freepostage@list_freepostage');
        $msg = app::get('topshop')->_('保存免邮成功');
        return $this->splash('success',$url,$msg,true);
    }

    public function delete_freepostage()
    {
        $apiData['shop_id'] = $this->shopId;
        $apiData['freepostage_id'] = input::get('freepostage_id');
        $url = url::action('topshop_ctl_promotion_freepostage@list_freepostage');
        try
        {
            app::get('topshop')->rpcCall('promotion.freepostage.delete', $apiData);
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error', $url, $msg, true);
        }
        $msg = app::get('topshop')->_('删除免邮成功');
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
        $freepostageId = input::get('freepostageId');
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
        if($freepostageId)
        {
            $objMdlFreepostageItem = app::get('syspromotion')->model('freepostage_item');
            $notEndItem = $objMdlFreepostageItem->getList('item_id', array('end_time|than'=>time() ,'freepostage_id'=>$freepostageId) );
            $pagedata['notEndItem'] = array_column($notEndItem, 'item_id');
        }
        else
        {
             $pagedata['notEndItem'] = array();
        }
        return response::json($pagedata);
    }

}
