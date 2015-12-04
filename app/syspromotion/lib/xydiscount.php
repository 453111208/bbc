<?php

class syspromotion_xydiscount extends syspromotion_abstract_promotions {

    public $promotionType = 'xydiscount';
    public $promotionTag = 'X件Y折';

    public function getXydiscountList($filter)
    {
        $filter['shop_id'] = $filter['shop_id'];
        return app::get('syspromotion')->model('xydiscount')->getList('*', $filter, '0', '-1', 'xydiscount_id DESC');
    }

    public function getXydiscount($xydiscountId)
    {
        return app::get('syspromotion')->model('xydiscount')->getRow('*', array('xydiscount_id'=>$xydiscountId));
    }
    //根据X件Y折id获取X件Y折的所有商品
    public function getXydiscountItems($xydiscountId)
    {
        return app::get('syspromotion')->model('xydiscount_item')->getList('*', array('xydiscount_id'=>$xydiscountId));
    }

    /**
     * @brief 删除X件Y折
     * @author lujy
     * @param $params array
     *
     * @return
     */
    public function deleteXydiscount($params)
    {
        $xydiscountId = $params['xydiscount_id'];
        if(!$xydiscountId)
        {
            throw new \LogicException('X件Y折促销id不能为空！');
            return false;
        }

        $objMdlXydiscount = app::get('syspromotion')->model('xydiscount');
        $xydiscountInfo = $objMdlXydiscount->getRow('shop_id, start_time',array('xydiscount_id'=>$xydiscountId,'shop_id'=>$params['shop_id']));
        if( $xydiscountInfo['shop_id'] != $params['shop_id'] )
        {
            throw new \LogicException('只能删除店铺所属的X件Y折促销！');
        }
        if( time() > $xydiscountInfo['start_time'] )
        {
            throw new \LogicException('X件Y折促销生效后则不可删除！');
        }
        $db = app::get('syspromotion')->database();
        $db->beginTransaction();

        try
        {
            // 删除X件Y折主表数据
            if( !$objMdlXydiscount->delete( array('xydiscount_id'=>$xydiscountId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除X件Y折失败'));
            }
            // 删除商品系统的商品关联促销表的促销
            $objMdlXydiscountItem = app::get('syspromotion')->model('xydiscount_item');
            $xydiscountItems = $objMdlXydiscountItem->getList('item_id', array('xydiscount_id'=>$xydiscountId));
            $itemIds = array_column($xydiscountItems,'item_id');
            $itemIdsStr = implode(',', $itemIds);
            $promotionInfo = app::get('syspromotion')->model('promotions')->getRow('promotion_id', array('rel_promotion_id'=>$xydiscountId, 'promotion_type'=>'xydiscount'));
            $flag = app::get('syspromotion')->rpcCall('item.promotiontag.delete',array('promotion_id'=>$promotionInfo['promotion_id'], 'item_ids'=>$itemIdsStr));
            // 新的商品及促销关联接口
            $flagNew = app::get('syspromotion')->rpcCall('item.promotion.deleteTag',array('promotion_id'=>$promotionInfo['promotion_id']));
            if(!$flag)
            {
                throw new \LogicException(app::get('syspromotion')->_('删除X件Y折失败'));
            }
            // 删除X件Y折关联的商品
            if( !$objMdlXydiscountItem->delete( array('xydiscount_id'=>$xydiscountId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除X件Y折失败'));
            }
            // 删除促销关联促销的X件Y折数据
            if( !$this->deletePromotions( array('rel_promotion_id'=>$xydiscountId,'promotion_type'=>'xydiscount') ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除X件Y折失败'));
            }
            $db->commit();

        }
        catch(\Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * 保存X件Y折促销
     * @param  array $data X件Y折促销传入数据
     * @return bool       是否保存成功
     */
    public function saveXydiscount($data) {
        $xydiscountData = $this->__preareData($data);
        $objMdlXydiscount = app::get('syspromotion')->model('xydiscount');

        $db = app::get('syspromotion')->database();
        $db->beginTransaction();
        try
        {
            if( $objMdlXydiscount->save($xydiscountData) )
            {
                // 保存促销关联信息到促销关联表
                $proData['rel_promotion_id'] = $xydiscountData['xydiscount_id'];
                $proData['shop_id']          = $xydiscountData['shop_id'];
                $proData['promotion_type']   = 'xydiscount';
                $proData['promotion_name']   = $xydiscountData['xydiscount_name'];
                $proData['promotion_tag']    = 'X件Y折';
                $proData['promotion_desc']   = $xydiscountData['xydiscount_desc'];
                $proData['used_platform']    = $xydiscountData['used_platform'];
                $proData['start_time']       = $xydiscountData['start_time'];
                $proData['end_time']         = $xydiscountData['end_time'];
                $proData['created_time']     = $xydiscountData['created_time'];
                $proData['check_status']     = $xydiscountData['xydiscount_status'];
                if(!$promotion_id = $this->savePromotions($proData))
                {
                    throw new \LogicException('X件Y折促销关联保存失败!');
                }
                if(!$this->__saveXydiscountItem($xydiscountData, $promotion_id))
                {
                    throw new \LogicException('X件Y折促销关联商品保存失败!');
                }
                $db->commit();
            }
            else
            {
                throw new \LogicException('X件Y折保存失败');
            }
        }
        catch(\LogicException $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }

    /**
     * 保存X件Y折促销关联的商品信息
     */
    private function __saveXydiscountItem(&$xydiscountData, $promotion_id)
    {
        //ajx改为调用search接口
        $searchParams = array(
            'item_id' => implode(',',$xydiscountData['rel_item_ids']),
            'fields' => 'item_id,title,image_default_id,cat_id,brand_id,price',
        );
        $itemsList = app::get('syspromotion')->rpcCall('item.search',$searchParams);
        if( empty($itemsList) ) return false;

        $newItemsList = array();
        foreach($itemsList['list'] as $item)
        {
            $newItemsList[$item['item_id']] = $item;
        }

        $objMdlXydiscountItem = app::get('syspromotion')->model('xydiscount_item');
        // 先删除X件Y折关联的商品
        $objMdlXydiscountItem->delete(array('xydiscount_id'=>$xydiscountData['xydiscount_id']));
        foreach($xydiscountData['rel_item_ids'] as $itemid)
        {
            $xydiscountRelationItem = array(
                'xydiscount_id' => $xydiscountData['xydiscount_id'],
                'item_id' => $itemid,
                'shop_id' => $xydiscountData['shop_id'],
                'promotion_tag' => $this->promotionTag,
                'leaf_cat_id' => $newItemsList[$itemid]['cat_id'],
                'brand_id' => $newItemsList[$itemid]['brand_id'],
                'title' => $newItemsList[$itemid]['title'],
                'price' => $newItemsList[$itemid]['price'],
                'image_default_id' => $newItemsList[$itemid]['image_default_id'],
                'start_time' => $xydiscountData['start_time'],
                'end_time' => $xydiscountData['end_time'],
            );
            $objMdlXydiscountItem->save($xydiscountRelationItem);
            // 保存促销标签到商品的促销标签表，方便搜索
            $apiData = array(
                'item_id' => $itemid,
                'promotion_id' => $promotion_id,
            );
            app::get('syspromotion')->rpcCall('item.promotiontag.update',$apiData);
            // 新的商品及促销关联接口
            app::get('syspromotion')->rpcCall('item.promotion.addTag', $apiData);
        }
        return true;
    }

    private function __preareData($data) {
        $aResult = array();
        $aResult = $data;

        $objMdlXydiscount = app::get('syspromotion')->model('xydiscount');
        if($data['xydiscount_id'])
        {
            $xydiscountInfo = $objMdlXydiscount->getRow('*', array('xydiscount_id'=>$data['xydiscount_id']));
            if( time() >= $xydiscountInfo['start_time'] )
            {
                throw new \LogicException('X件Y折促销生效时间内不可进行编辑!');
            }
        }
        else
        {
            $aResult['created_time'] = time();
        }
        if(!$data['xydiscount_name'])
        {
            throw new \LogicException("X件Y折名称不能为空!");
        }
        if(!$data['xydiscount_rel_itemids'])
        {
            throw new \LogicException("至少添加一个商品!");
        }
        $aResult['rel_item_ids'] = explode(',', $data['xydiscount_rel_itemids']);
        $objMdlXydiscountItem = app::get('syspromotion')->model('xydiscount_item');
        $itemList = $objMdlXydiscountItem->getList('xydiscount_id, title', array('item_id'=>$aResult['rel_item_ids'], 'end_time|than'=>time() ) );
        foreach($itemList as $v)
        {
            if($data['xydiscount_id'] )
            {
                if($v['xydiscount_id'] != $data['xydiscount_id'])
                {
                    throw new \LogicException("商品 {$v['title']} 已经参加别的X件Y折，同一个商品只能应用于一个有效的X件Y折促销中！");
                }
            }
            else
            {
                throw new \LogicException("商品 {$v['title']} 已经参加别的X件Y折，同一个商品只能应用于一个有效的X件Y折促销中！");
            }
        }
        if( $data['join_limit'] <= 0 )
        {
            throw new \LogicException('参与次数必须大于0!');
        }
        if( $data['start_time'] <= time() )
        {
            throw new \LogicException('X件Y折促销生效时间不能小于当前时间！');
        }
        if( $data['end_time'] <= $data['canuse_start_time'] )
        {
            throw new \LogicException('X件Y折促销结束时间不能小于开始时间！');
        }
        if( !$data['valid_grade'])
        {
            throw new \LogicException('至少选择一个会员等级');
        }
        // xy折扣的规则检验
        $rule = explode(',', $data['condition_value']);
        $ruleArray = array();
        foreach($rule as $k => $v)
        {
            $tmpXyValue = explode('|', $v);
            $ruleArray[$k]['limit_number'] = $tmpXyValue['0'];
            $ruleArray[$k]['discount'] = $tmpXyValue['1'];
        }
        $ruleLength = count($ruleArray);
        for($i=0; $i<$ruleLength; $i++)
        {
            if( $ruleArray[$i]['limit_number']<1 )
            {
                throw new \LogicException('件数必须大于0');
            }
            if( $ruleArray[$i]['discount'] > 100 || $ruleArray[$i]['discount'] < 1 )
            {
                throw new \LogicException('折扣必须在区间1%-100%！');
            }
            if( $i<$ruleLength-1 && $ruleArray[$i]['discount'] <= $ruleArray[$i+1]['discount'] )
            {
                throw new \LogicException('xy折扣必须依次递减！');
            }
        }
        //echo '<pre>';print_r($ruleArray);exit();
       /* if( $data['limit_number']<1)
        {
            throw new \LogicException('件数必须大于0');
        }
        if( $data['discount'] > 100 || $data['discount'] < 1 )
        {
            throw new \LogicException('折扣必须在区间1%-100%！');
        }*/


        $aResult['xydiscount_name'] = strip_tags($data['xydiscount_name']);
        $aResult['xydiscount_desc'] = strip_tags($data['xydiscount_desc']);
        $forPlatform = intval($data['used_platform']);
        $aResult['used_platform'] = $forPlatform ? $forPlatform : '0';

        $aResult['promotion_tag'] = $this->promotionTag;
        $aResult['xydiscount_status'] = 'agree';//@todo需要加规则进行检查，例如促销规则离谱需要平台审核

        return $aResult;
    }

}
