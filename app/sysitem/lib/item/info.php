<?php

class sysitem_item_info {

    /**
     * 获取商品详情，对应键值为itemId
     *
     * @param $itemId 商品ID
     * @param $fields 获取详情的字段
     */
    public function getItemDesc($itemId, $fields="*")
    {
        $fields = str_append($fields,'item_id');
        $objMdlItemDesc = app::get('sysitem')->model('item_desc');
        $itemInfoDesc = $objMdlItemDesc->getList($fields, array('item_id'=>$itemId));
        return array_bind_key($itemInfoDesc, 'item_id');
    }

    /**
     * 获取商品基本统计信息，对应键值为itemId
     *
     * @param $itemId 商品ID
     */
    public function getItemStore($itemId)
    {
        $objMdlItemStore = app::get('sysitem')->model('item_store');
        $tmpItemInfoStore = $objMdlItemStore->getList('*', array('item_id'=>$itemId));
        if( $tmpItemInfoStore )
        {
            foreach( $tmpItemInfoStore as $k=>$row )
            {
                $itemInfoStore[$row['item_id']]['store'] = $row['store'];
                $itemInfoStore[$row['item_id']]['freez'] = $row['freez'];
                $itemInfoStore[$row['item_id']]['realStore'] = $row['store']-$row['freez'];
            }
        }
        return $itemInfoStore;
    }

    /**
     * 获取商品上下架状态
     *
     * @param  $itemId
     * @param  $fields
     */
    public function getItemStatus($itemId, $fields='*')
    {
        $objMdlItemStatus = app::get('sysitem')->model('item_status');
        $itemInfoStatus = $objMdlItemStatus->getList($fields, array('item_id'=>$itemId) );
        return array_bind_key($itemInfoStatus, 'item_id');
    }

    /**
     * 统计商品上下架数量
     *
     * @param string $status onsale上架|instock下架
     * @param int $shopId 店铺ID，如果有店铺ID则统计店铺的商品
     *
     * @return numbr
     */
    public function countItemStatus($status='onsale', $shopId=null)
    {
        $objMdlItemStatus = app::get('sysitem')->model('item_status');

        if( $shopId )
        {
            $filter['shop_id'] = $shopId;
        }

        if( $status == 'onsale' )
        {
            $filter['approve_status'] = 'onsale';
        }
        else
        {
            $filter['approve_status'] = 'instock';
        }

        return $objMdlItemStatus->count($filter);
    }

    /**
     * 根据商品Id,获取商品的默认图片
     *
     * @param array|int itemId
     */
    public function getItemDefaultPic($itemId)
    {
        $objMdlItem = app::get('sysitem')->model('item');
        $itemInfo = $objMdlItem->getList('item_id,image_default_id', array('item_id'=>$itemId));
        return array_bind_key($itemInfo, 'item_id');
    }

    /**
     * 获取商品自然属性
     */
    public function getItemNatureProp($itemId, $small=false)
    {
        $objMdlItemNatureProps = app::get('sysitem')->model('item_nature_props');
        $itemNaturePropsArr = $objMdlItemNatureProps->getList('prop_id,prop_value_id,pv_type', array('item_id'=>$itemId));
        if( empty($itemNaturePropsArr) ) return array();
        if( $small == true ) return $itemNaturePropsArr;

        foreach( $itemNaturePropsArr as $row )
        {
            if( $row['pv_type'] == 'select' )
            {
                $propsIds[] = $row['prop_id'];
                $propValuesIds[] = $row['prop_value_id'];
            }
        }

        $objLibProps = kernel::single('syscategory_data_props');
        $propsData = $objLibProps->getPropsList($propsIds);
        foreach( $itemNaturePropsArr as $key=>$row )
        {
            if( $propsData[$row['prop_id']] )
            {
                $itemNaturePropsArr[$key]['props'] = $propsData[$row['prop_id']];
            }
        }

        $propValuesData = $objLibProps->getPropsValueList($propValuesIds);
        foreach( $itemNaturePropsArr as $key=>$row )
        {
            if( $propValuesData[$row['prop_value_id']] )
            {
                $itemNaturePropsArr[$key]['propValues'] = $propValuesData[$row['prop_value_id']];
            }
        }

        return $itemNaturePropsArr;
    }

    public function getItemList($itemIds, $rows='*', $fields=array() ,$option)
    {
        $start      = $option['start']   ? $option['start']   : 0;
        $limit      = $option['limit']   ? $option['limit']   : -1;
        $orderBy    = $option['orderBy'] ? $option['orderBy'] : 'item_id DESC';

        $itemList = app::get('sysitem')->model('item')->getList($rows, array('item_id'=>$itemIds), $start, $limit, $orderBy);
        if( empty($itemList) ) return $itemList;

        foreach((array)$itemList as $row )
        {
            if( $row['brand_id'] ) $brandIds[] =  $row['brand_id'];
            if( $row['cat_id'] ) $catIds[] =  $row['cat_id'];
        }

        if( $brandIds && $fields['brand_name'] )
        {
            $brandNameArr = app::get('sysitem')->rpcCall('category.brand.get.list',array('brand_id'=>implode(',',$brandIds),'fields'=>'brand_id,brand_name'));
        }

        if( $catIds && $fields['cat_name'] )
        {
            $catNameArr = app::get('sysitem')->rpcCall('category.cat.get.info',array('cat_id'=>implode(',',$catIds),'fields'=>'cat_name,cat_id'));
        }

        if( $fields['status'] )
        {
            $statusRows = 'item_id,'.$fields['status'];
            $itemStatus = $this->getItemStatus($itemIds, $statusRows);
        }

        foreach( (array)$itemList as $k=>$row )
        {
            if( $brandNameArr && $brandNameArr[$row['brand_id']] )
            {
                $itemList[$k]['brand_name'] = $brandNameArr[$row['brand_id']];
            }

            if( $catNameArr && $catNameArr[$row['cat_id']] )
            {
                $itemList[$k]['cat_name'] = $catNameArr[$row['cat_id']];
            }

            if( $itemStatus && $itemStatus[$row['item_id']] )
            {
                $itemList[$k] = array_merge($itemList[$k], $itemStatus[$row['item_id']]);
            }
        }
        if( $fields['promotion'] )
        {
            $promotionTag = app::get('sysitem')->model('item_tag_promotion')->getList('*',array('item_id'=>$itemIds));
            if( $promotionTag )
            {
                $promotionTag = array_bind_key($promotionTag,'item_id');
                foreach( $itemList as $key=>&$value )
                {
                    $value['promotion_ids'] = $promotionTag[$value['item_id']]['promotion_ids'];
                }
            }
        }

        return array_bind_key($itemList,'item_id');
    }

    /**
     * 获取商品的SKU信息
     *
     * @param int    $itemId 商品ID
     * @param string $rows 需要获取SKU的字段
     * @param bool   $isStore  是否需要获取库存数据，默认获取
     */
    public function getItemSkus($itemId, $rows="*", $isStore=true)
    {
        if( empty($itemId) ) return array();

        $filter['item_id'] = $itemId;
        $list = $this->__searchSkuList($filter, $rows, $isStore);

        return $list;
    }

    public function getSkusStore($skuIds)
    {
        $objMdlSkuStore = app::get('sysitem')->model('sku_store');

        $storeInfo = $objMdlSkuStore->getList('*',array('sku_id'=>$skuIds));
        foreach( $storeInfo as $k=>$row )
        {
            $data[$row['sku_id']] = $row;
            $data[$row['sku_id']]['realStore'] = $row['store']-$row['freez'];
        }

        return $data;
    }

    /**
     * 获取单个商品信息
     *
     * @param $filter 查询条件
     * @param $rows 需要获取的字段
     */
    public function getItemInfo($filter, $rows='*', $fields)
    {
        $itemId = $filter['item_id'];
        // 获取商品基本信息
        $objMdlItem = app::get('sysitem')->model('item');
        $itemInfo = $objMdlItem->getRow($rows, $filter);
        if( empty($itemInfo) ) return array();
        if( $itemInfo['list_image'] )
        {
            $images = explode(',',$itemInfo['list_image']);
            foreach( $images as $key=>$row )
            {
                if(!empty($row))
                {
                    $itemInfo['images'][$key] = $row;
                }
            }
        }

        if( $fields['promotion'] )
        {
            $promotionTag = app::get('sysitem')->model('item_tag_promotion')->getRow('*',array('item_id'=>$itemId));
            $itemInfo['promotion_ids'] = $promotionTag['promotion_ids'];
            $itemInfo['promotion_tags'] = $promotionTag['promotion_tags'];
        }

        //获取brand_id的名称
        if($itemInfo['brand_id'])
        {
            $brandParams['brand_id'] = $itemInfo['brand_id'];
            $brandParams['fields'] = 'brand_name,brand_alias,brand_logo';
            $brandInfo = app::get('sysitem')->rpcCall('category.brand.get.list',$brandParams);
            if($brandInfo)
            {
                $brandInfo = $brandInfo[0];
                $itemInfo['brand_name'] = $brandInfo['brand_name'];
                $itemInfo['brand_alias'] = $brandInfo['brand_alias'];
                $itemInfo['brand_logo'] = $brandInfo['brand_logo'];
            }
        }

        // 获取商品详情
        if( $fields['item_desc'] )
        {
            $descRows = $fields['item_desc'];
            $itemDesc = $this->getItemDesc($itemId, $descRows)[$itemId];
            foreach( $itemDesc as $key=>$value  )
            {
                if( !empty($itemDesc[$key]) && $key != 'item_id' )
                {
                    $itemInfo[$key] = stripslashes($value);
                }
            }
        }

        // 获取商品相关次数信息
        if( $fields['item_count'])
        {
            $countRows = $fields['item_count'];
            $objMdlItemCount = app::get('sysitem')->model('item_count');
            $itemInfoCount = $objMdlItemCount->getList($countRows, array('item_id'=>$itemId));
            $itemInfoCount = array_bind_key($itemInfoCount, 'item_id');
            $itemCount = $itemInfoCount[$itemId];
            if( $itemCount )
            {
                foreach( $itemCount as $cols=>$value)
                {
                    $itemInfo[$cols] = $value;
                }
            }
        }

        //获取商品库存和预售库存
        if( $fields['item_store'] )
        {
            $storeRows = $fields['item_store'];
            $itemStore = $this->getItemStore($itemId,$storeRows)[$itemId];
            if( $itemStore )
            {
                foreach( $itemStore as $storeKey=>$storeVal)
                {
                    $itemInfo[$storeKey] = $storeVal;
                }
            }
        }

        //获取商品上下架状态
        if($fields['item_status'])
        {
            $statusRows = $fields['item_status'];
            $itemStatus = $this->getItemStatus($itemId,$statusRows)[$itemId];
            if($itemStatus)
            {
                foreach($itemStatus as $statusK=>$statusV)
                {
                    $itemInfo[$statusK] = $statusV;
                }
            }
        }

        if($fields['sku'])
        {
            $skuRows = $fields['sku'];
            $itemInfo['sku'] = $this->__searchSkuList($filter,$skuRows);
        }

        if($fields['item_nature'])
        {
            $natureProp = $this->getItemNatureProp($itemId);
            foreach( $natureProp as $k=>$row )
            {
                $itemInfo['natureProps'][$k]['prop_id'] = $row['prop_id'];
                $itemInfo['natureProps'][$k]['prop_name'] = $row['props']['prop_name'];
                $itemInfo['natureProps'][$k]['prop_value_id'] = $row['prop_value_id'];
                $itemInfo['natureProps'][$k]['prop_value'] = $row['propValues']['prop_value'];
            }
        }
        return $itemInfo;
    }

    /**
     * 根据条件获取货品信息
     *
     * @param $skuIds 货品ID的数组
     * @param $row    SKU表中需要获取的字段
     * @param $store  是否需要获取库存信息
     *
     * @return array $skuList
     */
    private function __searchSkuList($filter, $rows='*', $isStore=true )
    {
        $rows = str_append($rows,'sku_id');
        $objMdlItem = app::get('sysitem')->model('sku');
        $list = $objMdlItem->getList($rows, $filter);
        if( empty($list) ) return array();

        $skuList = array_bind_key($list, 'sku_id');
        if( $isStore )
        {
            foreach( $skuList as $row )
            {
                $skuIds[] = $row['sku_id'];
            }
            $objMdlSkuStore = app::get('sysitem')->model('sku_store');

            $storeInfo = $objMdlSkuStore->getList('store,freez,sku_id',array('sku_id'=>$skuIds));
            foreach( $storeInfo as $row )
            {
                $skuList[$row['sku_id']]['store'] = intval($row['store']);
                $skuList[$row['sku_id']]['freez'] = intval($row['freez']);
                $skuList[$row['sku_id']]['realStore'] = $row['store'] - $row['freez'];
                $skuList[$row['sku_id']]['realStore'] = intval($skuList[$row['sku_id']]['realStore']);
            }
        }
        return $skuList;
    }

    /**
     * 根据sku_id的获取sku的基本信息
     *
     * @param $skuIds 货品ID的数组
     * @param $row    SKU表中需要获取的字段
     * @param $store  是否需要获取库存信息
     */
    public function getSkusList($skuIds, $rows="*", $isStore=true)
    {
        if( empty($skuIds) ) return array();

        $filter['sku_id'] = $skuIds;
        $list = $this->__searchSkuList($filter, $rows, $isStore);

        return $list;
    }

    /**
     * 获取单个SKU的基本信息
     *
     */
    public function getSkuInfo($skuId, $rows='*')
    {
        $objMdlItem = app::get('sysitem')->model('sku');
        $skuInfo = $objMdlItem->getRow($rows, array('sku_id'=>$skuId));
        $skuStore = $this->getSkusStore($skuId);
        $skuInfo['store'] = $skuStore[$skuId]['store'];
        $skuInfo['freez'] = $skuStore[$skuId]['freez'];
        $skuInfo['realStore'] = $skuStore[$skuId]['realStore'];
        return $skuInfo;
    }
}

