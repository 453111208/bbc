<?php

/**
 * @brief 商品数据处理
 */
class sysitem_data_item {

    /**
     * @brief 商品上下架
     * @author ajx
     * @param $params array item_ids
     * @param $status string onsale(上架、出售中) instock(下架、库中)
     * @param $msg string 处理结果
     *
     * @return bool
     */
    public function batchCloseItem($params,$status,&$msg)
    {
        if($params['item_id'][0] == '_ALL_')  unset($params);
        $ojbMdlItem = app::get('sysitem')->model('item_status');

        $updata['approve_status'] = $status;
        $updata['delist_time'] = time();

        $result = $ojbMdlItem->update($updata,$params);
        if($result)
        {
            $msg = app::get('sysitem')->_('商品下架成功');
            return true;
        }
        else
        {
            $msg = app::get('sysitem')->_('商品下架失败');
            return false;
        }
    }

    /**
     * @brief 商品上下架
     * @author Lujy
     * @param $params int itemId
     * @param $status string onsale(上架、出售中) instock(下架、库中)
     * @param $msg bool 处理结果
     *
     * @return bool
     */
    public function setSaleStatus($itemId, $status="onsale")
    {
        $ojbMdlItem = app::get('sysitem')->model('item_status');

        if($status=='onsale')
        {
            $objMdlStore = app::get('sysitem')->model('item_store');
            $storeInfo = $objMdlStore->getRow('*', array('item_id'=>$itemId));
            if( ($storeInfo['store'] - $storeInfo['freez']) <= 0 )
            {
                throw new \LogicException('库存为0不能上架');
            }
            $data = array('approve_status' => 'onsale','list_time'=>time());
        }
        if($status=='instock')
        {
            $data = array('approve_status' => 'instock','delist_time'=>time());
            //判断团购商品是否可以下架
            $params['item_id'] = $itemId;
            $activityStatus = app::get('sysitem')->rpcCall('promotion.activity.item.list',$params);
            //echo '<pre>';print_r($activityStatus);exit();
            if($activityStatus['status'])
            {
                $msg = app::get('sysitem')->_('该商品正在活动中不可修改！');
                throw new \LogicException($msg);
            }
        }
        $result = $ojbMdlItem->update($data, array('item_id' => intval($itemId) ) );
        if($result)
        {
            return true;
        }
        else
        {
            $status == 'onsale' ? $msg = app::get('sysitem')->_('商品上架失败') : $msg = app::get('sysitem')->_('商品下架失败');
            throw new \LogicException($msg);
        }
    }

    /**
     * @brief 删除商品
     * @author ajx
     * @param $params array  item_ids
     * @param $msg string 处理结果
     *
     * @return
     */
    public function goDelete($params,&$msg)
    {
        $ojbMdlItem = app::get('sysitem')->model('item');
        try
        {
            $result = $ojbMdlItem->doDelete($params['item_id']);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return false;
        }
        $msg = app::get('sysitem')->_('商品删除成功');
        return true;
    }

    private function __checkPost($postData)
    {
        //团购判断
        $params['item_id'] = $postData['item_id'];
        $activityStatus = app::get('sysitem')->rpcCall('promotion.activity.item.list',$params);
        if($activityStatus['status'])
        {
            $msg = app::get('sysitem')->_('该商品正在活动中不可修改！');
            throw new \LogicException($msg);
        }
        if( !$postData['cat_id'] )
        {
            $msg = app::get('sysitem')->_('商品分类不能为空');
            throw new \LogicException($msg);
        }
        if( !$postData['brand_id'] )
        {
            $msg = app::get('sysitem')->_('品牌不能为空');
            throw new \LogicException($msg);
        }

        if( !$postData['title'] )
        {
            $msg = app::get('sysitem')->_('商品名称不能为空');
            throw new \LogicException($msg);
        }
        if(isset($postData['item']['sub_title']) && $postData['item']['sub_title'] && strlen($postData['item']['sub_title']) > 150)
        {
            $msg = app::get('sysitem')->_( '商品卖点信息,请不要超过150个字符！' );
            throw new \LogicException($msg);
        }

        $objMdlCat = app::get('syscategory')->model('cat');
        $cat = $objMdlCat->getRow('level', array('cat_id'=>$postData['cat_id']));
        if($cat['level'] != 3 )
        {
            $msg = app::get('sysitem')->_('商品分类必须为三级分类');
            throw new \LogicException($msg);
        }

        $postData = $this->__checkSku($postData);
        return $postData;

    }

    private function __checkSku($postData)
    {
        $postData['sku'] = json_decode($postData['sku'],1);
        $postData['spec'] = json_decode($postData['spec'],1);

        // 无规格商品
        // if($postData && !$postData['spec'] && ( !is_array($postData['sku']) | !$postData['sku'] ) )
        if($postData && !$postData['spec'] && ( !reset($postData['sku']) || !$postData['sku'] ))
        {
            unset($postData['sku']);
            $postData['sku'][0]['sku_id'] = '';
            $postData['sku'][0]['price'] = $postData['price'];
            $postData['sku'][0]['cost_price'] = $postData['cost_price'] ? $postData['cost_price'] : 0;
            $postData['sku'][0]['mkt_price'] = $postData['mkt_price'] ? $postData['mkt_price'] : 0;
            $postData['sku'][0]['store'] = $postData['store'];
            $postData['sku'][0]['bn'] = $postData['bn'];
            $postData['sku'][0]['weight'] = $postData['weight'];
            $postData['nospec'] = '1';
        }

        foreach($postData['sku'] as $pk=>$pv)
        {
            if(is_array($postData['sku']) && is_array($postData['spec']))
            {
                if( count($pv['spec_desc']['spec_value_id']) < count($postData['item']['spec']) )
                {
                    $msg = app::get('sysitem')->_( '未选定全部规格' );
                    throw new \LogicException($msg);
                }
            }
        }
        if($postData['spec'])
        {
            $postData['nospec'] = '0';
        }
        else
        {
            foreach($postData['sku'] as $k=>$val)
            {
                $postData['sku'][$k]['price'] = $postData['price'];
                $postData['sku'][$k]['cost_price'] = $postData['cost_price'] ? $postData['cost_price'] : 0;
                $postData['sku'][$k]['mkt_price'] = $postData['mkt_price'] ? $postData['mkt_price'] : 0;
                $postData['sku'][$k]['store'] = $postData['store'];
                $postData['sku'][$k]['weight'] = $postData['weight'];
            }
            $postData['nospec'] = '1';
        }

        if(!$this->checkPriceWeight($postData['sku']))
        {
            $msg = app::get('sysitem')->_( '商品价格或重量格式错误' );
            throw new \LogicException($msg);
        }

        if(!$this->checkStore($postData['sku']))
        {
            $msg = app::get('sysitem')->_( '库存格式错误' );
            throw new \LogicException($msg);
        }
        return $postData;
    }

    function add($postData)
    {
        $postData['item'] = $this->__checkPost($postData['item']);

        $objMdlItem = app::get('sysitem')->model('item');
        $objMdlSku = app::get('sysitem')->model('sku');
        $objMdlCat = app::get('syscategory')->model('cat');

        $item = $this->_prepareItemData($postData);

        if($postData['item']['item_id'])
        {
            $params =array(
                'item_id' => $item['item_id'],
                'fields' => 'item_store,sku',
                'shop_id'=>$item['shop_id'],
            );
            $itemInfo= app::get('sysitem')->rpcCall('item.get',$params);
            if(!$itemInfo)
            {
                $msg = app::get('sysitem')->_('被编辑的商品数据异常,请重新操作');
                throw new \LogicException($msg);
            }
        }

        //检测原有库存和添加的库存是否合法
        if(reset($item['sku']))
        {
            $itemStore = 0;
            foreach ($itemInfo['sku'] as $key => $val)
            {
                $oldStore[$val['sku_id']] = $val['freez'];
            }

            foreach ($item['sku'] as $key => $value)
            {
                if($oldStore[$value['sku_id']] && $value['store'] < $value['freez'])
                {
                    $msg = app::get('sysitem')->_('货品库存不能小于冻结库存！');
                    throw new \LogicException($msg);
                    return false;
                }
                $itemStore += $value['store'];
            }

            if($itemStore != $item['store'])
            {
                $msg = app::get('sysitem')->_('货品库存总和不等于商品库存！');
                throw new \LogicException($msg);
                return false;
            }
        }

        if($item['store'] < $itemInfo['freez'])
        {
            $msg = app::get('sysitem')->_('商品库存不能小于'.$realStore);
            throw new \LogicException($msg);
        }


        if( $item['bn'] )
        {
            if( $this->__checkProductBn($item['bn'], $item['item_id'],$item['shop_id'],'item') )
            {
                $msg = app::get('sysitem')->_('您所填写的商品编号已被使用，请检查！');
                throw new \LogicException($msg);
            }
        }

        foreach($item['sku'] as $k => $p)
        {
            if(!$k && $k !== 0)
            {
                unset($item['sku'][$k]);
                continue;
            }
            if (is_null( $p['store'] ))
            {
                $item['sku'][$k]['freez'] = 0;
                $item['sku'][$k]['store'] = 0;
            }
            $item['sku'][$k]['sku_store']['store'] = $item['sku'][$k]['store'] ;
            $item['sku'][$k]['sku_store']['freez'] = $item['sku'][$k]['freez'];
            if(empty($p['bn'])) continue;
            if($this->__checkProductBn($p['bn'], $p['sku_id'], $item['shop_id'], 'sku') )
            {
                $msg = app::get('sysitem')->_('您所填写的货号已被使用，请检查！');
                throw new \LogicException($msg);
            }
        }
        if(!$item['sku'])
        {
            unset($item['sku']);
            unset($item['spec']);
        }

        // 描述
        $item['desc'] = array(
            'pc_desc'=>addslashes($item['desc']),
            'wap_desc'=>addslashes($item['wap_desc']),
        );

        // 商品状态
        $item['list_status'] = array();
        $item['list_status']['shop_id'] = $postData['item']['shop_id'];
        if($postData['item']['approve_status'] == 'instock')
        {
            $item['list_status']['approve_status'] = 'instock';
        }
        elseif($postData['item']['approve_status'] == 'onsale')
        {
            $item['list_status']['list_time'] = time();
            $item['list_status']['approve_status'] = 'onsale';
        }
        else
        {
            $item['list_status']['approve_status'] = 'instock';
        }

        // 自然属性
        $item['props'] = array();
        foreach($item['nature_props'] as $k=>$v)
        {
            $item['props'][] = array(
                'prop_id'=>$k,
                'prop_value_id'=>$v,
                'pv_number'=>$v,
                'pv_str'=>'',
                'modified_time'=>time(),
            );
        }
        unset($item['nature_props']);
        if(!$item['item_id'])
        {
            $item['created_time'] = time();
        }
        $item['modified_time'] = time();

        $db = app::get('sysitem')->database();
        $db->beginTransaction();
        try
        {
            if( !$objMdlItem->save($item) )
            {
                throw new \LogicException(app::get('sysitem')->_('保存商品失败！'));
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

    function _prepareItemData( &$data )
    {

        $objSku = app::get('sysitem')->model('sku');

        $item = $data['item'];

        if($item['item_id'])
        {
            $newSku = array_column($item['sku'],'sku_id');
            $skudata = $objSku->getList('sku_id,spec_info',array('item_id'=>$item['item_id']));
            foreach($skudata as $key=>$value)
            {
                if(in_array($value['sku_id'],$newSku))
                {
                    unset($skudata[$key]);
                    continue;
                }
                $oldSku[$value['sku_id']] = $value['spec_info'];
                $oldSkuId[] = $value['sku_id'];
            }

            if($oldSkuId)
            {
                $params['status'] = "WAIT_BUYER_PAY,WAIT_SELLER_SEND_GOODS,WAIT_BUYER_CONFIRM_GOODS";
                $params['sku_id'] = implode(',',$oldSkuId);
                $params['fields'] = 'sku_id' ;
                $trade = app::get('sysitem')->rpcCall('trade.order.list.get',$params);
                if($trade)
                {
                    foreach($trade as $val)
                    {
                        if($oldSku[$val['sku_id']])
                        {
                            $msg .= $oldSku[$val['sku_id']].";";
                        }
                    }
                    throw new \LogicException(app::get('sysitem')->_($msg.'的货品有未处理的订单'));
                }
            }
        }

        foreach((array)$item['sku'] as $key=>$val)
        {

            if( $val['store'] )
            {
                $item['sku'][$key]['store']= intval($val['store']);
            }
            $item['sku'][$key]['title']= $item['title'];

        }

        if(is_array($data['listimages']))
        {
            $item['image_default_id'] = $data['listimages'][0];
            $item['list_image'] = implode(',',$data['listimages']);
        }
        else
        {
            $item['image_default_id'] = null;
        }


        if( $item['spec'] )
        {
            $item['spec'] = $item['spec'];
            foreach( $item['spec'] as $specId=>&$specValue )
            {
                if( $specValue['show_type'] != 'image' || empty($specValue['option']) ) continue;
                foreach($specValue['option'] as $specValueId=>&$specValueData)
                {
                    $specValueData['spec_image_url'] = $data['images'][$specId.'_'.$specValueId];
                    $specValueData['spec_image'] = $data['images'][$specId.'_'.$specValueId];
                }
            }
        }
        else
        {
            $item['spec'] = null;
        }

        if( $item['params'] )
        {
            $itemParams = array();
            foreach( $item['params'] as $gpk => $gpv )
            {
                $itemParams[$data['itemParams']['group'][$gpk]][$data['itemParams']['item'][$gpk]] = $gpv;
            }
            $item['params'] = $itemParams;
        }

        foreach( $item['sku'] as $prok => $pro )
        {
            if( !$pro['sku_id'] || substr( $pro['sku_id'],0,4 ) == 'new' )
            {
                unset( $item['sku'][$prok]['sku_id'] );
            }
            $mprice = array();
            if( $pro['store'] === '' )
            {
                $item['sku'][$prok]['store'] = 0;
            }

            $item['sku'][$prok]['price'] = trim($item['sku'][$prok]['price']);
            $item['sku'][$prok]['cost_price'] = trim($item['sku'][$prok]['cost_price']) ? trim($item['sku'][$prok]['cost_price']) : 0;
            $item['sku'][$prok]['mkt_price'] = trim($item['sku'][$prok]['mkt_price']) ? trim($item['sku'][$prok]['mkt_price']) : 0;
            $item['sku'][$prok]['weight'] = trim($item['weight']);
        }

        return $item;
    }

    private function checkPriceWeight($data)
    {
        if(is_array($data))
        {
            foreach($data as $key=>&$val)
            {
                if(!empty($val['price']) && !is_numeric($val['price']))
                {
                    return false;
                }
            }
        }
        return true;
    }

    function checkStore($data)
    {
        if(is_array($data))
        {
            foreach($data as $key=>&$val)
            {
                if((!empty($val['store']) && !is_numeric($val['store'])) || $val['store'] < 0)
                {
                    return false;
                }
            }
        }
        return true;
    }

    private function __checkProductBn($bn, $itemId=0, $shopId=0,$type="item"){
        if(empty($bn)){
            return false;
        }
        $ojbMdlItem = app::get('sysitem')->model('item');
        $ojbMdlSku = app::get('sysitem')->model('sku');
        if($type == "item")
        {
            $data = $ojbMdlItem->getRow("item_id",['bn'=>$bn,'shop_id'=>$shopId,'item_id|noequal'=>$itemId]);
            if($data) return true;
        }
        else
        {
            $data = $ojbMdlSku->getRow("item_id",['bn'=>$bn,'sku_id|noequal'=>$itemId]);
            if($data) return true;
        }
        return false;
    }
    //根据规格id获取相关规格的商品
    public function hasPropItem($propId)
    {
        $specdescModel = app::get('sysitem')->model('spec_index');
        $itemList = $specdescModel->getList('item_id',array('prop_id'=>$propId));
        return $itemList;
    }

}
