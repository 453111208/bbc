<?php

class syspromotion_fulldiscount extends syspromotion_abstract_promotions {

    public $promotionType = 'fulldiscount';
    public $promotionTag = '满折';

    public function getFulldiscountList($filter)
    {
        $filter['shop_id'] = $filter['shop_id'];
        return app::get('syspromotion')->model('fulldiscount')->getList('*', $filter, '0', '-1', 'fulldiscount_id DESC');
    }

    public function getFulldiscount($fulldiscountId)
    {
        return app::get('syspromotion')->model('fulldiscount')->getRow('*', array('fulldiscount_id'=>$fulldiscountId));
    }
    //根据满折id获取满折的所有商品
    public function getFulldiscountItems($fulldiscountId)
    {
        return app::get('syspromotion')->model('fulldiscount_item')->getList('*', array('fulldiscount_id'=>$fulldiscountId));
    }

    /**
     * @brief 删除满折
     * @author lujy
     * @param $params array
     *
     * @return
     */
    public function deleteFulldiscount($params)
    {
        $fulldiscountId = $params['fulldiscount_id'];
        if(!$fulldiscountId)
        {
            throw new \LogicException('满折促销id不能为空！');
            return false;
        }

        $objMdlFulldiscount = app::get('syspromotion')->model('fulldiscount');
        $fulldiscountInfo = $objMdlFulldiscount->getRow('shop_id, start_time',array('fulldiscount_id'=>$fulldiscountId,'shop_id'=>$params['shop_id']));
        if( $fulldiscountInfo['shop_id'] != $params['shop_id'] )
        {
            throw new \LogicException('只能删除店铺所属的满折促销！');
        }
        if( time() > $fulldiscountInfo['start_time'] )
        {
            throw new \LogicException('满折促销生效后则不可删除！');
        }
        $db = app::get('syspromotion')->database();
        $db->beginTransaction();

        try
        {
            // 删除满折主表数据
            if( !$objMdlFulldiscount->delete( array('fulldiscount_id'=>$fulldiscountId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除满折失败'));
            }
            // 删除商品系统的商品关联促销表的促销
            $objMdlFulldiscountItem = app::get('syspromotion')->model('fulldiscount_item');
            $fulldiscountItems = $objMdlFulldiscountItem->getList('item_id', array('fulldiscount_id'=>$fulldiscountId));
            $itemIds = array_column($fulldiscountItems,'item_id');
            $itemIdsStr = implode(',', $itemIds);
            $promotionInfo = app::get('syspromotion')->model('promotions')->getRow('promotion_id', array('rel_promotion_id'=>$fulldiscountId, 'promotion_type'=>'fulldiscount'));
            $flag = app::get('syspromotion')->rpcCall('item.promotiontag.delete',array('promotion_id'=>$promotionInfo['promotion_id'], 'item_ids'=>$itemIdsStr));
            // 新的商品及促销关联接口
            $flagNew = app::get('syspromotion')->rpcCall('item.promotion.deleteTag',array('promotion_id'=>$promotionInfo['promotion_id']));
            if(!$flag)
            {
                throw new \LogicException(app::get('syspromotion')->_('删除满折失败'));
            }
            // 删除满折关联的商品
            if( !$objMdlFulldiscountItem->delete( array('fulldiscount_id'=>$fulldiscountId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除满折失败'));
            }
            // 删除促销关联促销的满折数据
            if( !$this->deletePromotions( array('rel_promotion_id'=>$fulldiscountId,'promotion_type'=>'fulldiscount') ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除满折失败'));
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
     * 保存满折促销
     * @param  array $data 满折促销传入数据
     * @return bool       是否保存成功
     */
    public function saveFulldiscount($data) {
        $fulldiscountData = $this->__preareData($data);
        $objMdlFulldiscount = app::get('syspromotion')->model('fulldiscount');

        $db = app::get('syspromotion')->database();
        $db->beginTransaction();
        try
        {
            if( $objMdlFulldiscount->save($fulldiscountData) )
            {
                // 保存促销关联信息到促销关联表
                $proData['rel_promotion_id'] = $fulldiscountData['fulldiscount_id'];
                $proData['shop_id']          = $fulldiscountData['shop_id'];
                $proData['promotion_type']   = 'fulldiscount';
                $proData['promotion_name']   = $fulldiscountData['fulldiscount_name'];
                $proData['promotion_tag']    = '满折';
                $proData['promotion_desc']   = $fulldiscountData['fulldiscount_desc'];
                $proData['used_platform']    = $fulldiscountData['used_platform'];
                $proData['start_time']       = $fulldiscountData['start_time'];
                $proData['end_time']         = $fulldiscountData['end_time'];
                $proData['created_time']     = $fulldiscountData['created_time'];
                $proData['check_status']     = $fulldiscountData['fulldiscount_status'];
                if(!$promotion_id = $this->savePromotions($proData))
                {
                    throw new \LogicException('满折促销关联保存失败!');
                }
                if(!$this->__saveFulldiscountItem($fulldiscountData, $promotion_id))
                {
                    throw new \LogicException('满折促销关联商品保存失败!');
                }
                $db->commit();
            }
            else
            {
                throw new \LogicException('满折保存失败');
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
     * 保存满折促销关联的商品信息
     */
    private function __saveFulldiscountItem(&$fulldiscountData, $promotion_id)
    {
        //ajx改为调用search接口
        $searchParams = array(
            'item_id' => implode(',',$fulldiscountData['rel_item_ids']),
            'fields' => 'item_id,title,image_default_id,cat_id,brand_id,price',
        );

        $itemsList = app::get('syspromotion')->rpcCall('item.search',$searchParams);
        if( empty($itemsList) ) return false;

        $newItemsList = array();
        foreach($itemsList['list'] as $item)
        {
            $newItemsList[$item['item_id']] = $item;
        }

        $objMdlFulldiscountItem = app::get('syspromotion')->model('fulldiscount_item');
        // 先删除满折关联的商品
        $objMdlFulldiscountItem->delete(array('fulldiscount_id'=>$fulldiscountData['fulldiscount_id']));
        foreach($fulldiscountData['rel_item_ids'] as $itemid)
        {
            $fulldiscountRelationItem = array(
                'fulldiscount_id' => $fulldiscountData['fulldiscount_id'],
                'item_id' => $itemid,
                'shop_id' => $fulldiscountData['shop_id'],
                'promotion_tag' => $this->promotionTag,
                'leaf_cat_id' => $newItemsList[$itemid]['cat_id'],
                'brand_id' => $newItemsList[$itemid]['brand_id'],
                'title' => $newItemsList[$itemid]['title'],
                'price' => $newItemsList[$itemid]['price'],
                'image_default_id' => $newItemsList[$itemid]['image_default_id'],
                'start_time' => $fulldiscountData['start_time'],
                'end_time' => $fulldiscountData['end_time'],
            );
            $objMdlFulldiscountItem->save($fulldiscountRelationItem);
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

        $objMdlFulldiscount = app::get('syspromotion')->model('fulldiscount');
        if($data['fulldiscount_id'])
        {
            $fulldiscountInfo = $objMdlFulldiscount->getRow('*', array('fulldiscount_id'=>$data['fulldiscount_id']));
            if( time() >= $fulldiscountInfo['start_time'] )
            {
                throw new \LogicException('满折促销生效时间内不可进行编辑!');
            }
        }
        else
        {
            $aResult['created_time'] = time();
        }
        if(!$data['fulldiscount_name'])
        {
            throw new \LogicException("满折名称不能为空!");
        }
        if(!$data['fulldiscount_rel_itemids'])
        {
            throw new \LogicException("至少添加一个商品!");
        }
        $aResult['rel_item_ids'] = explode(',', $data['fulldiscount_rel_itemids']);
        $objMdlFulldiscountItem = app::get('syspromotion')->model('fulldiscount_item');
        $itemList = $objMdlFulldiscountItem->getList('fulldiscount_id, title', array('item_id'=>$aResult['rel_item_ids'], 'end_time|than'=>time() ) );
        foreach($itemList as $v)
        {
            if($data['fulldiscount_id'] )
            {
                if($v['fulldiscount_id'] != $data['fulldiscount_id'])
                {
                    throw new \LogicException("商品 {$v['title']} 已经参加别的满折，同一个商品只能应用于一个有效的满折促销中！");
                }
            }
            else
            {
                throw new \LogicException("商品 {$v['title']} 已经参加别的满折，同一个商品只能应用于一个有效的满折促销中！");
            }
        }
        if( $data['join_limit'] <= 0 )
        {
            throw new \LogicException('参与次数必须大于0!');
        }
        if( $data['start_time'] <= time() )
        {
            throw new \LogicException('满折促销生效时间不能小于当前时间！');
        }
        if( $data['end_time'] <= $data['canuse_start_time'] )
        {
            throw new \LogicException('满折促销结束时间不能小于开始时间！');
        }
        if( !$data['valid_grade'])
        {
            throw new \LogicException('至少选择一个会员等级');
        }

        // 满折金额的规则检验
        $rule = explode(',', $data['condition_value']);
        $ruleArray = array();
        foreach($rule as $k => $v)
        {
            $tmpFullDiscountValue = explode('|', $v);
            $ruleArray[$k]['full'] = $tmpFullDiscountValue['0'];
            $ruleArray[$k]['discount'] = $tmpFullDiscountValue['1'];
        }
        $ruleLength = count($ruleArray);
        for($i=0; $i<$ruleLength; $i++)
        {
            if( $ruleArray[$i]['discount'] > 100 || $ruleArray[$i]['discount'] < 1 )
            {
                throw new \LogicException('折扣必须在区间1%-100%！');
            }
            if( $i<$ruleLength-1 && $ruleArray[$i]['full'] >= $ruleArray[$i+1]['full'] )
            {
                throw new \LogicException('满折金额的(满足金额)必须依次递增！');
            }
            // if( $i<$ruleLength-1 && $ruleArray[$i]['discount'] >= $ruleArray[$i+1]['discount'] )
            // {
            //     throw new \LogicException('满折金额的折扣必须依次递增！');
            // }
        }

        $aResult['fulldiscount_name'] = strip_tags($data['fulldiscount_name']);
        $aResult['fulldiscount_desc'] = strip_tags($data['fulldiscount_desc']);
        $forPlatform = intval($data['used_platform']);
        $aResult['used_platform'] = $forPlatform ? $forPlatform : '0';

        $aResult['promotion_tag'] = $this->promotionTag;
        $aResult['fulldiscount_status'] = 'agree';//@todo需要加规则进行检查，例如促销规则离谱需要平台审核

        return $aResult;
    }

}
