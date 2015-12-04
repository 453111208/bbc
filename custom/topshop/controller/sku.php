<?php

/**
 * @brief
 */
class topshop_ctl_sku extends topshop_controller{

    public function getNatureProps()
    {
        $catId = intval(input::get('cat_id'));
        $itemId = intval(input::get('item_id'));
        $propSql="SELECT sc.*, t1.item_prop_name, t1.item_prop_id 
                            FROM syscategory_cat sc 
                            LEFT JOIN
                            (SELECT
                                scrp.cat_id, sip.item_prop_name, sip.item_prop_id
                            FROM
                                syscategory_cat_rel_prop scrp
                            LEFT JOIN syscategory_item_prop sip ON scrp.prop_id = sip.item_prop_id) t1
                            ON sc.cat_id = t1.cat_id
                            WHERE sc.`level` = 3 AND sc.cat_id = ".$catId ;
        $propList = app::get("base")->database()->executeQuery($propSql)->fetchAll();     
        $pagedata['nature_props']=$propList;            
        // $pagedata['nature_props'] = app::get('topshop')->rpcCall('category.catprovalue.get',array('cat_id'=>$catId,'type'=>'nature'));
        // if($itemId)
        // {
        //     $item_nature_props = app::get('topshop')->rpcCall('item.get.nature.prop',array('item_id'=>$itemId));
        //     $isSelectionProp = array();
        //     foreach($item_nature_props as $v)
        //     {
        //         $isSelectionProp[$v['prop_id']] = $v['prop_value_id'];
        //     }
        //     $isSelectionPropArr = array();
        //     foreach ($pagedata['nature_props'] as &$v)
        //     {
        //         if(array_key_exists($v['prop_id'], $isSelectionProp))
        //         {
        //             $v['selected_prop_value_id'] = $isSelectionProp[$v['prop_id']];
        //         }
        //     }
        // }

        // if(!$pagedata['nature_props'] = )
        // {
        //     return '';
        // }
        return view::make('topshop/item/props/nature.html',$pagedata);
    }

    public function getSpecProps()
    {
        $catId = intval(input::get('cat_id'));
        $itemId = intval(input::get('item_id'));
        if($itemId)
        {
            $itemSpecInfo = $this->getSpecPropsByItemId($itemId);
            $params_spec = $itemSpecInfo['spec'];
            //规格数据
            $result  = $this->set_spec($catId, $params_spec);
            $pagedata['spec_props'] = $result['all_spec'];
        }
        else
        {
            $specProp = app::get('topshop')->rpcCall('category.catprovalue.get',array('cat_id'=>$catId,'type'=>'spec'));
            foreach($specProp as $key=>$value)
            {
                $specProp[$key][$value['prop_value_id']] = $value;
            }
            $pagedata['spec_props'] = $specProp;
        }

        if(!$pagedata['spec_props'])
        {
            return '';
        }
        return view::make('topshop/item/props/spec.html', $pagedata);
    }

    public function getParams()
    {
        $catId = intval(input::get('cat_id'));
        $itemId = intval(input::get('item_id'));
        if($itemId)
        {
            $itemInfo = app::get('topshop')->rpcCall('item.get',array('item_id'=>$itemId,'fields'=>'item_id, cat_id, params'));
            if($itemInfo['params']){
                $pagedata['params'] = $itemInfo['params'];
            }else{
                $catInfo = app::get('topshop')->rpcCall('category.cat.get.info',array('cat_id'=>$catId,'fields'=>'params'));
                $pagedata['params'] = $catInfo[$catId]['params'];
            }
        }
        else
        {
            $catInfo = app::get('topshop')->rpcCall('category.cat.get.info',array('cat_id'=>$catId,'fields'=>'params'), 'seller');
            $pagedata['params'] = $catInfo[$catId]['params'];
        }

        if(!$pagedata['params'])
        {
            return '';
        }
        return view::make('topshop/item/params.html', $pagedata);
    }

    function getSpecPropsByItemId($itemId)
    {
        $itemInfo = app::get('topshop')->rpcCall('item.get',array('item_id'=>$itemId,'fields'=>'spec_desc'));

        $objMdlProps = app::get('syscategory')->model('props');
        if( $itemInfo['spec_desc'] && is_array( $itemInfo['spec_desc'] ) )
        {
            $propIds = implode(',',array_keys($itemInfo['spec_desc']));
            $propsList = app::get('topshop')->rpcCall('category.prop.list',array('prop_id'=>$propIds));
            foreach( $itemInfo['spec_desc'] as $specId => $spec )
            {
                $itemInfo['spec'][$specId] = $propsList[$specId];
                foreach( $spec as $pSpecId => $specValue )
                {
                    $itemInfo['spec'][$specId]['option'][$pSpecId] = array_merge( array('private_spec_value_id'=>$pSpecId), $specValue );
                }
            }
        }

        unset($itemInfo['spec_desc']);
        return $itemInfo;
    }

    /**
     * 编辑sku的销售属性信息
     */
    function set_spec_index($catId,$item_id)
    {
        $catId = intval(input::get('cat_id'));
        $item_id = intval(input::get('item_id'));
        $objMdlItem = app::get('sysitem')->model('item');

        if($_GET['nospec'] == 1)
        {
            // $type_id = $_GET['type_id'];
            $params_spec = array();
        }
        else
        {
            //当item_id为空时，数据库会拉出最后一条信息（兼容导致的），如果类目不同，会带入其它的规格，导致系统判断商品添加规格存在没有选择的项
            if($item_id)
            {
                $itemInfo = $this->getSpecPropsByItemId($item_id);
                $params_spec = $itemInfo['spec'];
            }
        }

        //规格数据
        $result  = $this->set_spec($catId, $params_spec);

        $pagedata['spec_props'] = $result['all_spec'];

        if(!$_GET['nospec'])//如果开启过规格则需要返回货品数据和选中的规格数据
        {
            $pagedata['selection_spec'] = $result['selection_spec'];
            $products = $this->getProducts($item_id);
            $active = $this->_pre_recycle_spec($item_id,$products);
            $pagedata['activeSpec'] = $active['activeSpec'];//不能删除的规格(有活动订单)
        }
        $pagedata['selection_spec_json'] = $result['selection_spec'];// ? json_encode($result['selection_spec']) : false;
        $pagedata['products'] = $products;// ? json_encode($products) : false;
        sort($active['activeProducts']);
        $pagedata['activeProducts'] = $active['activeProducts'];//json_encode($active['activeProducts']); //不能删除的货品(有活动订单)

        return response::json($pagedata);exit;
    }

    /**
     * 规格相关数据处理
     *
     */
    public function set_spec($catId, $params_spec){
        $objMdlProps = app::get('syscategory')->model('props');
        $subSdf = array(
            'prop_value' =>array('*')
        );
        if($params_spec)
        {
            $prop_id = implode(',',array_keys($params_spec));
            $specifications = app::get('topshop')->rpcCall('category.catprovalue.get',array('cat_id'=>$catId,'prop_id'=>$prop_id,'type'=>'spec'));
        }
        else
        {
            return false;
        }

        //默认规格图片
        $this->default_spec_image = app::get('syscategory')->getConf('spec.default.pic');
        $this->default_spec_image_url =  base_storager::modifier($this->default_spec_image);

        //选中规格数据
        $selectSpecData = array();
        if($params_spec)
        {
            $selectSpecData = $this->_select_spec($params_spec);
        }

        $specAll = $this->_set_spec_all($specifications, $selectSpecData);

        $aReturn = array(
            'all_spec' => $specAll,
            'selection_spec' => $selectSpecData['selectionSpec'],
        );

        return $aReturn;
    }

    /*
     * 选中的规格数据处理
     * */
    private function _select_spec($paramsSpec){
        if($this->pagedata['goods_spec_images'])
        {
            $specGoodsImagesArr = app::get('image')->model('image')->getList('image_id,s_url,url',array('image_id'=>$this->pagedata['goods_spec_images']));
            $resource_host_url = kernel::get_resource_host_url();
            foreach($specGoodsImagesArr as $row)
            {
                $row['s_url'] = $row['s_url'] ? $row['s_url'] : $row['url'];
                if($row['s_url'] &&!strpos($row['s_url'],'://'))
                {
                    $row['s_url'] = $resource_host_url.'/'.$row['s_url'];
                }
                $goodsImages[$row['image_id']] = $row['s_url'];
            }
        }


        foreach((array)$paramsSpec as $specId=>$selectSpecRow)
        {
            $selectionSpec[$specId]  = $selectSpecRow;

            //当前规格选中数量
            $selectCount[$specId] = count($selectSpecRow['option']);

            //选中的规格
            foreach($selectSpecRow['option'] as $privateSpecValueId=>$option )
            {

                $selectSpecValueId[] = $option['spec_value_id'];

                unset($selectionSpec[$specId]['option'][$privateSpecValueId]);

                if( $selectSpecRow['show_type'] == 'image' )
                {
                    $option['spec_image_url'] = $option['spec_image'] ? base_storager::modifier($option['spec_image']) : $this->default_spec_image_url;
                    $option['spec_image'] = $option['spec_image'] ? $option['spec_image'] : $this->default_spec_image;
                }
                $selectionSpec[$specId]['option'][$option['spec_value_id']] = $option;
            }
        }

        return array(
            'selectionSpec' => $selectionSpec,
            'selectCount' => $selectCount,
            'selectSpecValueId' => $selectSpecValueId,
        );

    }//end function

    /*
     * 处理规格数据（在规格数据的原基础上新增是否被选中和该规格多少被选中）
     *
     */
    private function _set_spec_all($specifications, $selectSpecData){
        $all_spec = array();
        foreach($specifications as $key=>$row)
        {
            $all_spec[$row['prop_id']] = $row;
            $all_spec[$row['prop_id']]['selectCount'] = $selectSpecData['selectCount'][$row['prop_id']] ? $selectSpecData['selectCount'][$row['prop_id']] : 0;
            foreach( $row['prop_value'] as $spec_value_id=>$spec_value_row )
            {

                if($row['show_type'] == 'image')
                {
                    $all_spec[$row['prop_id']]['prop_value'][$spec_value_id]['spec_image'] = $spec_value_row['spec_image'] ? $spec_value_row['spec_image'] : $this->default_spec_image;
                }

                $selectSpecValue = $selectSpecData['selectionSpec'][$row['prop_id']]['option'][$spec_value_id];
                if( $selectSpecValue )
                {
                    $all_spec[$row['prop_id']]['prop_value'][$spec_value_id]['private_spec_value_id'] = $selectSpecValue['private_spec_value_id'];
                }
                else
                {
                    $all_spec[$row['prop_id']]['prop_value'][$spec_value_id]['private_spec_value_id'] = time().$spec_value_id;
                }

                #规格中的规格值是否被选中
                if( $selectSpecData['selectSpecValueId'] &&  in_array($spec_value_id,$selectSpecData['selectSpecValueId']) )
                {
                    $all_spec[$row['prop_id']]['prop_value'][$spec_value_id]['select'] = true;
                }
                else
                {
                    $all_spec[$row['prop_id']]['prop_value'][$spec_value_id]['select'] = false;
                }

            }
        }
        return $all_spec;
    }

    /*
     * 获取待编辑货品
     * */
    public function getProducts($itemId=0){
        if(!$itemId) return false;
        $productData = app::get('topshop')->rpcCall('item.sku.list',array('item_id'=>$itemId));
        foreach((array)$productData as $row)
        {
            $unique_id = $this->get_unique_id($row['spec_desc']['spec_value_id']);
            $row['freez'] = ($row['freez'] !== null) ? $row['freez'] : '0';
            $returnData[$unique_id] = $row;
        }

        return $returnData;
    }



/*-----------------------以上为编辑货品显示数据处理函数-----------------------------*/

    /*
     * 每个货品的唯一键值(根据每个货品的规格ID生成) 在js中需要此键值来加载对应的数据
     * */
    private function get_unique_id($spec){
        $str = implode(';',$spec);
        return substr(md5($str),0,10);
    }

    /*
     * 加载页面判断是否需要有不能删除的规格和货品(对应的订单没有完成)
     * */
    private function _pre_recycle_spec($item_id,$products){
        if(!$item_id)  return array();
        //活动的货品
        $activeProducts = $this->_get_active_products($item_id);
        $activeProducts = $activeProducts['activeProducts'];

        $activeSpec = array();
        foreach($products as $uid=>$prow){
            $specValueIds = $prow['spec_desc']['spec_value_id'];
            if( in_array($prow['sku_id'],$activeProducts) ){
                $activeSpec = array_merge($activeSpec,$specValueIds);
            }
        }

        $return = array(
            'activeProducts' => $activeProducts,//活动的货品ID 不能删除
            'activeSpec' => array_unique($activeSpec) //活动的规格 不能删除
        );

        return $return;
    }

    /*
     * 获取当前商品活动的货品
     * */
    private function _get_active_products($item_id)
    {
        //获取到当前商品的活动订单
        $params['item_id'] = $item_id;
        $params['fields'] = "oid,sku_id";
        $ordersItemsData = app::get('topshop')->rpcCall('trade.order.list.get',$params);
        foreach($ordersItemsData as $row)
        {
            $tmpActiveProducts[$row['oid']][] = $row['sku_id'];
            $orderids[] = $row['oid'];
        }

        $params['status'] = "WAIT_BUYER_PAY,WAIT_SELLER_SEND_GOODS,WAIT_BUYER_CONFIRM_GOODS";
        $params['oids'] = implode(',',$orderids);
        $params['fields'] = "oid,sku_id";
        $ordersData = app::get('topshop')->rpcCall('trade.order.list.get',$params);
        $activeProducts= array();
        foreach($ordersData as $row)
        {
            $activeProducts = array_merge($activeProducts,$tmpActiveProducts[$row['oid']]);
        }
        //活动订单货品ID
        $activeOrderProducts = $activeProducts;

        //去除重复
        $activeProducts = array_unique($activeProducts);
        $return = array(
            'activeProducts' => $activeProducts,
        );
        return $return;
    }
}

