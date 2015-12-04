<?php

class syspromotion_fullminus extends syspromotion_abstract_promotions {

    public $promotionType = 'fullminus';
    public $promotionTag = '满减';

    public function getFullminusList($filter)
    {
        $filter['shop_id'] = $filter['shop_id'];
        return app::get('syspromotion')->model('fullminus')->getList('*', $filter, '0', '-1', 'fullminus_id DESC');
    }

    public function getFullminus($fullminusId)
    {
        return app::get('syspromotion')->model('fullminus')->getRow('*', array('fullminus_id'=>$fullminusId));
    }
    //根据满减id获取满减的所有商品
    public function getFullminusItems($fullminusId)
    {
        return app::get('syspromotion')->model('fullminus_item')->getList('*', array('fullminus_id'=>$fullminusId));
    }

    /**
     * @brief 删除满减
     * @author lujy
     * @param $params array
     *
     * @return
     */
    public function deleteFullminus($params)
    {
        $fullminusId = $params['fullminus_id'];
        if(!$fullminusId)
        {
            throw new \LogicException('满减促销id不能为空！');
            return false;
        }

        $objMdlFullminus = app::get('syspromotion')->model('fullminus');
        $fullminusInfo = $objMdlFullminus->getRow('shop_id, start_time',array('fullminus_id'=>$fullminusId,'shop_id'=>$params['shop_id']));
        if( $fullminusInfo['shop_id'] != $params['shop_id'] )
        {
            throw new \LogicException('只能删除店铺所属的满减促销！');
        }
        if( time() > $fullminusInfo['start_time'] )
        {
            throw new \LogicException('满减促销生效后则不可删除！');
        }
        $db = app::get('syspromotion')->database();
        $db->beginTransaction();

        try
        {
            // 删除满减主表数据
            if( !$objMdlFullminus->delete( array('fullminus_id'=>$fullminusId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除满减失败'));
            }
            // 删除商品系统的商品关联促销表的促销
            $objMdlFullminusItem = app::get('syspromotion')->model('fullminus_item');
            $fullminusItems = $objMdlFullminusItem->getList('item_id', array('fullminus_id'=>$fullminusId));
            $itemIds = array_column($fullminusItems,'item_id');
            $itemIdsStr = implode(',', $itemIds);
            $promotionInfo = app::get('syspromotion')->model('promotions')->getRow('promotion_id', array('rel_promotion_id'=>$fullminusId, 'promotion_type'=>'fullminus'));
            $flag = app::get('syspromotion')->rpcCall('item.promotiontag.delete',array('promotion_id'=>$promotionInfo['promotion_id'], 'item_ids'=>$itemIdsStr));
            // 新的商品及促销关联接口
            $flagNew = app::get('syspromotion')->rpcCall('item.promotion.deleteTag',array('promotion_id'=>$promotionInfo['promotion_id']));
            if(!$flag)
            {
                throw new \LogicException(app::get('syspromotion')->_('删除满减失败'));
            }
            // 删除满减关联的商品
            if( !$objMdlFullminusItem->delete( array('fullminus_id'=>$fullminusId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除满减失败'));
            }
            // 删除促销关联促销的满减数据
            if( !$this->deletePromotions( array('rel_promotion_id'=>$fullminusId,'promotion_type'=>'fullminus') ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除满减失败'));
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
     * 保存满减促销
     * @param  array $data 满减促销传入数据
     * @return bool       是否保存成功
     */
    public function saveFullminus($data) {
        $fullminusData = $this->__preareData($data);
        $objMdlFullminus = app::get('syspromotion')->model('fullminus');

        $db = app::get('syspromotion')->database();
        $db->beginTransaction();
        try
        {
            if( $objMdlFullminus->save($fullminusData) )
            {
                // 保存促销关联信息到促销关联表
                $proData['rel_promotion_id'] = $fullminusData['fullminus_id'];
                $proData['shop_id']          = $fullminusData['shop_id'];
                $proData['promotion_type']   = 'fullminus';
                $proData['promotion_name']   = $fullminusData['fullminus_name'];
                $proData['promotion_tag']    = '满减';
                $proData['promotion_desc']   = $fullminusData['fullminus_desc'];
                $proData['used_platform']    = $fullminusData['used_platform'];
                $proData['start_time']       = $fullminusData['start_time'];
                $proData['end_time']         = $fullminusData['end_time'];
                $proData['created_time']     = $fullminusData['created_time'];
                $proData['check_status']     = $fullminusData['fullminus_status'];
                if(!$promotion_id = $this->savePromotions($proData))
                {
                    throw new \LogicException('满减促销关联保存失败!');
                }
                if(!$this->__saveFullminusItem($fullminusData, $promotion_id))
                {
                    throw new \LogicException('满减促销关联商品保存失败!');
                }
                $db->commit();
            }
            else
            {
                throw new \LogicException('满减保存失败');
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
     * 保存满减促销关联的商品信息
     */
    private function __saveFullminusItem(&$fullminusData, $promotion_id)
    {
        //ajx改为调用search接口
        $searchParams = array(
            'item_id' => implode(',',$fullminusData['rel_item_ids']),
            'fields' => 'item_id,title,image_default_id,cat_id,brand_id,price',
        );
        $itemsList = app::get('syspromotion')->rpcCall('item.search',$searchParams);
        if( empty($itemsList) ) return false;

        $newItemsList = array();
        foreach($itemsList['list'] as $item)
        {
            $newItemsList[$item['item_id']] = $item;
        }

        $objMdlFullminusItem = app::get('syspromotion')->model('fullminus_item');
        // 先删除满减关联的商品
        $objMdlFullminusItem->delete(array('fullminus_id'=>$fullminusData['fullminus_id']));
        foreach($fullminusData['rel_item_ids'] as $itemid)
        {
            $fullminusRelationItem = array(
                'fullminus_id' => $fullminusData['fullminus_id'],
                'item_id' => $itemid,
                'shop_id' => $fullminusData['shop_id'],
                'promotion_tag' => $this->promotionTag,
                'leaf_cat_id' => $newItemsList[$itemid]['cat_id'],
                'brand_id' => $newItemsList[$itemid]['brand_id'],
                'title' => $newItemsList[$itemid]['title'],
                'price' => $newItemsList[$itemid]['price'],
                'image_default_id' => $newItemsList[$itemid]['image_default_id'],
                'start_time' => $fullminusData['start_time'],
                'end_time' => $fullminusData['end_time'],
            );
            $objMdlFullminusItem->save($fullminusRelationItem);
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

        $objMdlFullminus = app::get('syspromotion')->model('fullminus');
        if($data['fullminus_id'])
        {
            $fullminusInfo = $objMdlFullminus->getRow('*', array('fullminus_id'=>$data['fullminus_id']));
            if( time() >= $fullminusInfo['start_time'] )
            {
                throw new \LogicException('满减促销生效时间内不可进行编辑!');
            }
        }
        else
        {
            $aResult['created_time'] = time();
        }
        if(!$data['fullminus_name'])
        {
            throw new \LogicException("满减名称不能为空!");
        }
        if(!$data['fullminus_rel_itemids'])
        {
            throw new \LogicException("至少添加一个商品!");
        }
        $aResult['rel_item_ids'] = explode(',', $data['fullminus_rel_itemids']);
        $objMdlFullminusItem = app::get('syspromotion')->model('fullminus_item');
        $itemList = $objMdlFullminusItem->getList('fullminus_id, title', array('item_id'=>$aResult['rel_item_ids'], 'end_time|than'=>time() ) );
        foreach($itemList as $v)
        {
            if($data['fullminus_id'] )
            {
                if($v['fullminus_id'] != $data['fullminus_id'])
                {
                    throw new \LogicException("商品 {$v['title']} 已经参加别的满减，同一个商品只能应用于一个有效的满减促销中！");
                }
            }
            else
            {
                throw new \LogicException("商品 {$v['title']} 已经参加别的满减，同一个商品只能应用于一个有效的满减促销中！");
            }
        }
        if( $data['join_limit'] <= 0 )
        {
            throw new \LogicException('参与次数必须大于0!');
        }
        if( $data['start_time'] <= time() )
        {
            throw new \LogicException('满减促销生效时间不能小于当前时间！');
        }
        if( $data['end_time'] <= $data['canuse_start_time'] )
        {
            throw new \LogicException('满减促销结束时间不能小于开始时间！');
        }
        if( !$data['valid_grade'])
        {
            throw new \LogicException('至少选择一个会员等级');
        }

        // 满减金额的规则检验
        $rule = explode(',', $data['condition_value']);
        $ruleArray = array();
        foreach($rule as $k => $v)
        {
            $tmpFullMinusValue = explode('|', $v);
            $ruleArray[$k]['full'] = $tmpFullMinusValue['0'];
            $ruleArray[$k]['minus'] = $tmpFullMinusValue['1'];
        }
        $ruleLength = count($ruleArray);
        for($i=0; $i<$ruleLength; $i++)
        {
            if( $ruleArray[$i]['full'] <= $ruleArray[$i]['minus'] )
            {
                throw new \LogicException('满减金额的(满足金额)必须大于(减去金额)！');
            }
            if( $i<$ruleLength-1 && $ruleArray[$i]['full'] >= $ruleArray[$i+1]['full'] )
            {
                throw new \LogicException('满减金额的(满足金额)必须依次递增！');
            }
            if( $i<$ruleLength-1 && $ruleArray[$i]['minus'] >= $ruleArray[$i+1]['minus'] )
            {
                throw new \LogicException('满减金额的(减去金额)必须依次递增！');
            }
        }

        $aResult['fullminus_name'] = strip_tags($data['fullminus_name']);
        $aResult['fullminus_desc'] = strip_tags($data['fullminus_desc']);
        $forPlatform = intval($data['used_platform']);
        $aResult['used_platform'] = $forPlatform ? $forPlatform : '0';

        $aResult['promotion_tag'] = $this->promotionTag;
        $aResult['fullminus_status'] = 'agree';//@todo需要加规则进行检查，例如促销规则离谱需要平台审核

        return $aResult;
    }

}
