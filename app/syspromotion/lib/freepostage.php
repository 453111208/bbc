<?php

class syspromotion_freepostage extends syspromotion_abstract_promotions {

    public $promotionType = 'freepostage';
    public $promotionTag = '免邮';

    public function getFreepostage($freepostageId)
    {
        return app::get('syspromotion')->model('freepostage')->getRow('*', array('freepostage_id'=>$freepostageId));
    }
    //根据免邮id获取免邮的所有商品
    public function getFreepostageItems($freepostageId)
    {
        return app::get('syspromotion')->model('freepostage_item')->getList('*', array('freepostage_id'=>$freepostageId));
    }

    /**
     * @brief 删除免邮
     * @author lujy
     * @param $params array
     *
     * @return
     */
    public function deleteFreepostage($params)
    {
        $freepostageId = $params['freepostage_id'];
        if(!$freepostageId)
        {
            throw new \LogicException('免邮促销id不能为空！');
            return false;
        }

        $objMdlFreepostage = app::get('syspromotion')->model('freepostage');
        $freepostageInfo = $objMdlFreepostage->getRow('shop_id, start_time',array('freepostage_id'=>$freepostageId,'shop_id'=>$params['shop_id']));
        if( $freepostageInfo['shop_id'] != $params['shop_id'] )
        {
            throw new \LogicException('只能删除店铺所属的免邮促销！');
        }
        if( time() > $freepostageInfo['start_time'] )
        {
            throw new \LogicException('免邮促销生效后则不可删除！');
        }
        $db = app::get('syspromotion')->database();
        $db->beginTransaction();

        try
        {
            // 删除免邮主表数据
            if( !$objMdlFreepostage->delete( array('freepostage_id'=>$freepostageId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除免邮失败'));
            }
            // 删除商品系统的商品关联促销表的促销
            $objMdlFreepostageItem = app::get('syspromotion')->model('freepostage_item');
            $freepostageItems = $objMdlFreepostageItem->getList('item_id', array('freepostage_id'=>$freepostageId));
            $itemIds = array_column($freepostageItems,'item_id');
            $itemIdsStr = implode(',', $itemIds);
            $promotionInfo = app::get('syspromotion')->model('promotions')->getRow('promotion_id', array('rel_promotion_id'=>$freepostageId, 'promotion_type'=>'freepostage'));
            $flag = app::get('syspromotion')->rpcCall('item.promotiontag.delete',array('promotion_id'=>$promotionInfo['promotion_id'], 'item_ids'=>$itemIdsStr));
            // 新的商品及促销关联接口
            $flagNew = app::get('syspromotion')->rpcCall('item.promotion.deleteTag',array('promotion_id'=>$promotionInfo['promotion_id']));
            if(!$flag)
            {
                throw new \LogicException(app::get('syspromotion')->_('删除免邮失败'));
            }
            // 删除免邮关联的商品
            if( !$objMdlFreepostageItem->delete( array('freepostage_id'=>$freepostageId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除免邮失败'));
            }
            // 删除促销关联促销的免邮数据
            if( !$this->deletePromotions( array('rel_promotion_id'=>$freepostageId,'promotion_type'=>'freepostage') ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除免邮失败'));
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
     * 保存免邮促销
     * @param  array $data 免邮促销传入数据
     * @return bool       是否保存成功
     */
    public function saveFreepostage($data) {
        $freepostageData = $this->__preareData($data);
        $objMdlFreepostage = app::get('syspromotion')->model('freepostage');

        $db = app::get('syspromotion')->database();
        $db->beginTransaction();
        try
        {
            if( $objMdlFreepostage->save($freepostageData) )
            {
                // 保存促销关联信息到促销关联表
                $proData['rel_promotion_id'] = $freepostageData['freepostage_id'];
                $proData['shop_id']          = $freepostageData['shop_id'];
                $proData['promotion_type']   = 'freepostage';
                $proData['promotion_name']   = $freepostageData['freepostage_name'];
                $proData['promotion_tag']    = '免邮';
                $proData['promotion_desc']   = $freepostageData['freepostage_desc'];
                $proData['used_platform']    = $freepostageData['used_platform'];
                $proData['start_time']       = $freepostageData['start_time'];
                $proData['end_time']         = $freepostageData['end_time'];
                $proData['created_time']     = $freepostageData['created_time'];
                $proData['check_status']     = $freepostageData['freepostage_status'];
                if(!$promotion_id = $this->savePromotions($proData))
                {
                    throw new \LogicException('免邮促销关联保存失败!');
                }
                if(!$this->__saveFreepostageItem($freepostageData, $promotion_id))
                {
                    throw new \LogicException('免邮促销关联商品保存失败!');
                }
                $db->commit();
            }
            else
            {
                throw new \LogicException('免邮保存失败');
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
     * 保存免邮促销关联的商品信息
     */
    private function __saveFreepostageItem(&$freepostageData, $promotion_id)
    {
        //ajx改为调用search接口
        $searchParams = array(
            'item_id' => implode(',',$freepostageData['rel_item_ids']),
            'fields' => 'item_id,title,image_default_id,cat_id,brand_id,price',
        );

        $itemsList = app::get('syspromotion')->rpcCall('item.search',$searchParams);
        if( empty($itemsList) ) return false;

        $newItemsList = array();
        foreach($itemsList['list'] as $item)
        {
            $newItemsList[$item['item_id']] = $item;
        }

        $objMdlFreepostageItem = app::get('syspromotion')->model('freepostage_item');
        // 先删除免邮关联的商品
        $objMdlFreepostageItem->delete(array('freepostage_id'=>$freepostageData['freepostage_id']));
        foreach($freepostageData['rel_item_ids'] as $itemid)
        {
            $freepostageRelationItem = array(
                'freepostage_id' => $freepostageData['freepostage_id'],
                'item_id' => $itemid,
                'shop_id' => $freepostageData['shop_id'],
                'promotion_tag' => $this->promotionTag,
                'leaf_cat_id' => $newItemsList[$itemid]['cat_id'],
                'brand_id' => $newItemsList[$itemid]['brand_id'],
                'title' => $newItemsList[$itemid]['title'],
                'price' => $newItemsList[$itemid]['price'],
                'image_default_id' => $newItemsList[$itemid]['image_default_id'],
                'start_time' => $freepostageData['start_time'],
                'end_time' => $freepostageData['end_time'],
            );
            $objMdlFreepostageItem->save($freepostageRelationItem);
            // 保存促销标签到商品的促销标签表，方便搜索
            $apiData = array(
                'item_id' => $itemid,
                'promotion_id' => $promotion_id,
            );
            app::get('syspromotion')->rpcCall('item.promotiontag.update', $apiData);
            app::get('syspromotion')->rpcCall('item.promotion.addTag', $apiData);
        }
        return true;
    }

    private function __preareData($data) {
        $aResult = array();
        $aResult = $data;

        $objMdlFreepostage = app::get('syspromotion')->model('freepostage');
        if($data['freepostage_id'])
        {
            $freepostageInfo = $objMdlFreepostage->getRow('*', array('freepostage_id'=>$data['freepostage_id']));
            if( time() >= $freepostageInfo['start_time'] )
            {
                throw new \LogicException('免邮促销生效时间内不可进行编辑!');
            }
        }
        else
        {
            $aResult['created_time'] = time();
        }
        if(!$data['freepostage_name'])
        {
            throw new \LogicException("免邮名称不能为空!");
        }
        if(!$data['freepostage_rel_itemids'])
        {
            throw new \LogicException("至少添加一个商品!");
        }
        $aResult['rel_item_ids'] = explode(',', $data['freepostage_rel_itemids']);
        $objMdlFreepostageItem = app::get('syspromotion')->model('freepostage_item');
        $itemList = $objMdlFreepostageItem->getList('freepostage_id, title', array('item_id'=>$aResult['rel_item_ids'], 'end_time|than'=>time() ) );
        foreach($itemList as $v)
        {
            if($data['freepostage_id'] )
            {
                if($v['freepostage_id'] != $data['freepostage_id'])
                {
                    throw new \LogicException("商品 {$v['title']} 已经参加别的免邮，同一个商品只能应用于一个有效的免邮促销中！");
                }
            }
            else
            {
                throw new \LogicException("商品 {$v['title']} 已经参加别的免邮，同一个商品只能应用于一个有效的免邮促销中！");
            }
        }
        if( !in_array($data['condition_type'], array('money', 'quantity')) )
        {
            throw new \LogicException('免邮类型没有选择！');
        }
        if( $data['condition_type'] == 'money' )
        {
            if($data['limit_money']<=0)
            {
                throw new \LogicException('金额必须大于0');
            }
            unset($aResult['limit_quantity']);
        }

        if( $data['condition_type'] == 'quantity' )
        {
            if($data['limit_quantity']<=0)
            {
                throw new \LogicException('件数必须大于0');
            }
            unset($aResult['limit_money']);
        }
        if( $data['start_time'] <= time() )
        {
            throw new \LogicException('免邮促销生效时间不能小于当前时间！');
        }
        if( $data['end_time'] <= $data['start_time'] )
        {
            throw new \LogicException('免邮促销结束时间不能小于开始时间！');
        }
        if( !$data['valid_grade'])
        {
            throw new \LogicException('至少选择一个会员等级');
        }

        // 免邮金额的规则检验

        $aResult['freepostage_name'] = strip_tags($data['freepostage_name']);
        $aResult['freepostage_desc'] = strip_tags($data['freepostage_desc']);
        $forPlatform = intval($data['used_platform']);
        $aResult['used_platform'] = $forPlatform ? $forPlatform : '0';

        $aResult['promotion_tag'] = $this->promotionTag;
        $aResult['freepostage_status'] = 'agree';//@todo需要加规则进行检查，例如促销规则离谱需要平台审核

        return $aResult;
    }

}
