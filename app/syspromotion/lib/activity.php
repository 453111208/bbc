<?php

class syspromotion_activity {


    /**
     * @brief 获取活动状态
     * @author gjp
     * @param $params array
     *
     * @return
     */
    public function getActivityStatus($params)
    {

        $objMdlActivityItem = app::get('syspromotion')->model('activity_item');
        $objMdlActivity = app::get('syspromotion')->model('activity_register');
        $list = $objMdlActivityItem->getList('activity_id',array('item_id'=>$params['item_id']));
        
        if($list)
        {
            foreach($list as $key=>$value)
            {
                $data = $objMdlActivity->getRow('*',array('activity_id'=>$value['activity_id']));
                //echo '<pre>';print_r($data);exit();
                if($data['apply_begin_time'] < time() && time() < $data['end_time'] && $value['verify_status']!='refuse')
                {
                    $result = 1;
                }
                else
                {
                    $result = 0;
                }
            }
        }
        else
        {
            $result = 0;
        }
        return $result;
        #code
    }
    /**
     * @brief 删除活动
     * @author lujy
     * @param $params array
     *
     * @return
     */
    public function deleteActivity($params)
    {
        $objMdlActivity = app::get('syspromotion')->model('activity');
        $list = $objMdlActivity->getList('activity_id,apply_begin_time',array('activity_id'=>$params));
        if($list)
        {
            $result = true;
            foreach($list as $key=>$value)
            {
                if($value['apply_begin_time'] < time())
                {
                    $result = false;
                    $msg = "活动报名已经开始，不可删除";
                }
                else
                {
                    $return = $objMdlActivity->delete(array('activity_id'=>$value['activity_id']));
                    if(!$return)
                    {
                        $result = false;
                        $msg = "删除失败";
                    }
                }
            }
            if(!$result)
            {
                throw new LogicException($msg);
            }
        }
        return true;
        #code
    }

    /**
     * 保存活动
     * @param  array $data 活动传入数据
     * @return bool       是否保存成功
     */
    public function saveActivity($data)
    {
        $activityData = $this->__preareData($data);
        $objMdlActivity = app::get('syspromotion')->model('activity');

        $db = app::get('syspromotion')->database();
        $db->beginTransaction();
        try
        {
            if( !$objMdlActivity->save($activityData) )
            {
                throw \LogicException('活动保存失败');
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;

    }

    private function __preareData($data) {
        $aResult = array();
        $aResult = $data;

        if($data['activity_id'])
        {
            $objMdlActivity = app::get('syspromotion')->model('activity');
            $activityInfo = $objMdlActivity->getRow('*',array('activity_id'=>$data['activity_id']));
            if( time() >= $activityInfo['start_time'] )
            {
                throw new \LogicException('活动生效开始时间后则不可进行编辑!');
            }
        }
        else
        {
            $aResult['created_time'] = time();
        }
       /* if( $data['buy_limit'] <= 0 )
        {
            throw new \LogicException('用户限购数量要大于0!');
        }
        if( $data['discount_max'] <= $data['discount_min'])
        {
            throw new \LogicException('折扣范围必须由小到大！');
        }

        if($data['apply_begin_time'] < time())
        {
            throw new \LogicException('活动报名的开始时间必须大于当前时间！');
        }

        if( $data['apply_end_time'] <= $data['apply_begin_time'] )
        {
            throw new \LogicException('活动报名结束时间必须大于报名的开始时间！');
        }

        if( $data['release_time'] <= $data['apply_end_time']  )
        {
            throw new \LogicException('发布时间必须大于报名结束时间！');
        }

        if( $data['start_time'] <= $data['release_time'] )
        {
            throw new \LogicException('活动生效时间必须大于活动发布时间！');
        }

        if(  $data['end_time'] <= $data['start_time'] )
        {
            throw new \LogicException('活动生效结束时间必须大于活动开始时间！');
        }

        if( !$data['shoptype'])
        {
            throw new \LogicException('至少选择一种店铺类型！');
        }
        if( !$data['limit_cat'])
        {
            throw new \LogicException('至少选择一种平台商品类目！');
        }*/

        $aResult['activity_name'] = strip_tags($data['activity_name']);
        $aResult['activity_desc'] = strip_tags($data['activity_desc']);
        $aResult['shoptype'] = implode(',',$data['shoptype']);
        // $forPlatform = intval($data['used_platform']);
        // $aResult['used_platform'] = $forPlatform ? $forPlatform : '0';
        return $aResult;
    }


    public function getList($row,$filter,$offset=0, $limit=200, $orderBy=null)
    {
        $objMdlActivity = app::get('syspromotion')->model('activity');
        $activity = $objMdlActivity->getList($row,$filter,$offset,$limit,$orderBy);
        return $activity;
    }

    public function countActivity($filter)
    {
        $objMdlActivity = app::get('syspromotion')->model('activity');
        return $objMdlActivity->count($filter);
    }

    public function countActivityItem($filter)
    {
        $objMdlActivityItem = app::get('syspromotion')->model('activity_item');
        return $objMdlActivityItem->count($filter);
    }

    public function getInfo($row,$filter)
    {
        $objMdlActivity = app::get('syspromotion')->model('activity');
        $activity = $objMdlActivity->getRow($row,$filter);

        //如果查询的字段中有店铺类型，需要显示店铺中文描述
        if(strpos($row,'shoptype') || $row == "*" || $row == "shoptype")
        {
            $shoptype = $activity['shoptype'];
            $activity['shoptype'] = $this->__getShopType($shoptype);
        }
        //如果查询的字段中包含类目，需要查询类目相关的所有内容
        if(strpos($row,'limit_cat') || $row == "*" || $row == "limit_cat")
        {
            $cat = $activity['limit_cat'];
            $activity['limit_cat'] = $this->__getCat($cat);
        }
        return $activity;
    }

    private function __getShopType($params)
    {
        // 获取店铺类型
        $shopType = app::get('syspromotion')->rpcCall('shop.type.get',array('shop_type'=>$params));
        foreach($shopType as $value)
        {
            $type[$value['shop_type']] = $value['name'];
        }
        return $type;
    }

    private function __getCat($params)
    {
        $params = implode(',',$params);
        //获取类目
        $cat = app::get('syspromotion')->rpcCall('category.cat.get.info',array('cat_id' => $params,'level' =>1));
        foreach($cat as $value)
        {
            $data[$value['cat_id']] = $value['cat_name'];
        }
        return $data;
    }

    public function getItemList($row,$filter,$offset=0, $limit=200, $orderBy=null)
    {
        $objMdlItemActivity = app::get('syspromotion')->model('activity_item');
        $activityItem = $objMdlItemActivity->getList($row,$filter,$offset,$limit,$orderBy);
        return $activityItem;
    }

    public function getItemInfo($row,$filter)
    {
        $objMdlItemActivity = app::get('syspromotion')->model('activity_item');
        $activityItem = $objMdlItemActivity->getRow($row,$filter);
        return $activityItem;
    }

    /**
     * 保存活动报名
     * @param  array $data 活动报名数据
     * @return bool       是否保存成功
     */
    public function saveRegisterActivity($data)
    {
        $activityItem = array();
        $itemIds = array();
        foreach($data['item_info'] as $v)
        {
            $apiParams['fields'] = 'item_id,cat_id,title,image_default_id,price';
            $apiParams['item_id'] = $v['item_id'];
            $itemsInfo = app::get('topshop')->rpcCall('item.get', $apiParams);
            $itemIds[]=$v['item_id'];
            $activityItem[] = array(
                'activity_id' => $data['activity_id'],
                'shop_id' => $data['shop_id'],
                'item_id' => $v['item_id'],
                'cat_id' => $itemsInfo['cat_id'],
                'title' => $itemsInfo['title'],
                'item_default_image' => $itemsInfo['image_default_id'],
                'price' => $itemsInfo['price'],
                'activity_price' => $v['activity_price'],
                'start_time' => $data['activity_info']['start_time'],
                'end_time' => $data['activity_info']['end_time'],
                'activity_tag' => $data['activity_info']['activity_tag'],
            );
        }
        $registerData = array(
            'activity_id' => $data['activity_id'],
            'shop_id' => $data['shop_id'],
            'modified_time' => time(),
        );
        //商品参加团购判断重复添加
        $objMdlActivityItem = app::get('syspromotion')->model('activity_item');
        $itemList = $objMdlActivityItem->getList('activity_id, title,verify_status', array('item_id'=>$itemIds, 'end_time|than'=>time() ) );
        foreach($itemList as $v)
        {
            if($data['activity_id'] && $v['verify_status']!='refuse')
            {
                if($v['activity_id'] != $data['activity_id'])
                {
                    throw new \LogicException("商品 {$v['title']} 已经参加别的团购，同一个商品只能应用于一个有效的团购促销中！");
                }
            }
            /*else
            {
                throw new \LogicException("商品 {$v['title']} 已经参加别的团购，同一个商品只能应用于一个有效的团购促销中！");
            }*/
        }

        $objMdlActivityRegister = app::get('syspromotion')->model('activity_register');
        $objMdlActivityItem = app::get('syspromotion')->model('activity_item');

        $db = app::get('syspromotion')->database();
        $db->beginTransaction();
        try
        {
            // 保存报名的基础信息
            $objMdlActivityRegister->delete(array('activity_id'=>$registerData['activity_id'], 'shop_id'=>$registerData['shop_id']));
            if( !$objMdlActivityRegister->save($registerData) )
            {
                throw \LogicException('活动报名保存失败');
            }
            // 先删除报名关联的商品
            $objMdlActivityItem->delete(array('activity_id'=>$registerData['activity_id'], 'shop_id'=>$registerData['shop_id']));
            // 保存报名的商品信息
            foreach ($activityItem as $vItem)
            {
                if( !$objMdlActivityItem->save($vItem) )
                {
                    throw \LogicException("活动报名商品保存失败");
                }
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;

    }

    public function setMainpush($params)
    {
        $objMdlActivity = app::get('syspromotion')->model('activity');
        $result = $objMdlActivity->update(array( 'mainpush' => 0));
        if(!$result)
        {
            throw \LogicException("取消原有主推活动失败");
        }
        $params['mainpush'] = 1;
        $result = $objMdlActivity->save($params);
        if(!$result)
        {
            throw \LogicException("设置主推活动失败");
        }
        return true;
    }

}
