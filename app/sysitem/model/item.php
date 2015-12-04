<?php
class sysitem_mdl_item extends dbeav_model{
	/**
	* @var bool 启用标签
	*/
    var $has_tag = true;

    function __construct(&$app){
        parent::__construct($app);
        $this->schema['columns']['status'] = array('type'=>'string');
    }

    public $defaultOrder = array('modified_time','DESC');

    public $has_many = array(
        'sku' => 'sku:contrast',
        'props' => 'item_nature_props:replace:item_id^item_id',
    );

    public $has_one = array(
        'desc' => 'item_desc@sysitem:contrast:item_id^item_id',
        'item_count' => 'item_count@sysitem:contrast:item_id^item_id',
        'item_store' => 'item_store@sysitem:contrast:item_id^item_id',
        'list_status' => 'item_status@sysitem:contrast:item_id^item_id',
    );

    public function save(&$item,$mustUpdate = null, $mustInsert = false){
        if( !$item['bn'] ) $item['bn'] = strtoupper(uniqid('g'));
        if( array_key_exists( 'spec',$item ) )
        {
            if( $item['spec'] )
            {
                foreach( $item['spec'] as $gSpecId => $gSpecOption )
                {
                    $item['spec_desc'][$gSpecId] = $gSpecOption['option'];
                }
            }
            else
            {
                $item['spec_desc'] = null;
            }
        }
        $itemStatus = false;
        $store = 0;
        is_array($item['sku']) or $item['sku'] = array();
        $bnList = array();
        foreach( $item['sku'] as $pk => $pv )
        {
            if( !$pv['bn'] && !$item['nospec'])
            {
                $item['sku'][$pk]['bn'] = strtoupper(uniqid('s'));
            }
            if($item['nospec'])
            {
                $item['sku'][$pk]['bn'] = $item['bn'];
            }

            if( array_key_exists( $item['sku'][$pk]['bn'], $bnList ) )
            {
                return null;
            }
            $bnList[$item['sku'][$pk]['bn']] = 1;
            if( $pv['store'] === null || $pv['store'] === '' )
            {
                $store = 0;
            }
            else
            {
                if ($store !== null)
                {
                    $store += $pv['store'];
                }
            }
        }
        if($item['sku'])
        {
            $item['store'] = $store;
        }
        else
        {
            unset($item['sku']);
            $sku_mdl = app::get('sysitem')->model('sku');
            $sku = $sku_mdl->getList('sku_id', array('item_id'=>$item['item_id']));
            foreach($sku as $k=>$v)
            {
                // $item['sku'][$k]['name'] = $item['name'];
                $item['sku'][$k]['sku_id'] = $v['sku_id'];
            }
        }
        unset($item['spec']);

        // 为了生成数量统计表的数据
        if(!isset($item['item_count']))
        {
            $item['item_count'] = array('item_id'=>$item['item_id']);
        }
        // 为了生成商品主表的库存，预占库存的数据
        if(!isset($item['item_store']))
        {
            $item['item_store'] = array('item_id'=>$item['item_id'], 'store'=>$item['store']);
        }
        $item['modified_time'] = time();
        $rs = parent::save($item, $mustUpdate);

        if($item['sku'])
        {
            $this->createSpecIndex($item);
        }
        return $rs;
    }

    public function createSpecIndex($item)
    {
        $objMdlSpecIndex = app::get('sysitem')->model('spec_index');
        $objMdlSpecIndex->delete( array('item_id'=>$item['item_id']) );
        foreach( $item['sku'] as $pro )
        {
            if( $pro['spec_desc'] )
            {
                foreach( $pro['spec_desc']['spec_value_id'] as $specId => $specValueId )
                {
                    $data = array(
                        'cat_id' => $item['cat_id'],
                        'prop_id' => $specId,
                        'prop_value_id' => $specValueId,
                        'item_id' => $item['item_id'],
                        'sku_id' => $pro['sku_id'],
                    );
                    $objMdlSpecIndex->save($data);
                }
            }
        }
    }


    function dump($filter,$field = '*',$subSdf = null){
        $dumpData = parent::dump($filter,$field,$subSdf);

        $oSpec = app::get('syscategory')->model('props');
        if( $dumpData['spec_desc'] && is_array( $dumpData['spec_desc'] ) ){
            foreach( $dumpData['spec_desc'] as $specId => $spec ){
                // $dumpData['spec'][$specId] = $oSpec->dump($specId,'*');
                $dumpData['spec'][$specId] = $oSpec->getRow('*',array('prop_id'=>$specId));
                foreach( $spec as $pSpecId => $specValue ){
                    $dumpData['spec'][$specId]['option'][$pSpecId] = array_merge( array('private_spec_value_id'=>$pSpecId), $specValue );
                }
            }
        }

        unset($dumpData['spec_desc']);
        return $dumpData;
    }

    public function getList($cols='*', $filter=array(), $offset=0, $limit=200, $orderBy=null)
    {
        if($filter['status'])
        {
            $filter['approve_status'] = $filter['status'];
            unset($filter['status']);
        }

        $data = kernel::single('search_object')->instance('item')
            ->page($offset, $limit)
            ->orderBy($orderBy)
            ->search($cols,$filter);

        return $data['list'];
    }

    public function count($filter=null)
    {
        if($filter['status'])
        {
            $filter['approve_status'] = $filter['status'];
            unset($filter['status']);
        }
        return kernel::single('search_object')->instance('item')->count($filter);
    }

    /**
     * 重写搜索的下拉选项方法
     * @param null
     * @return null
     */
    public function searchOptions(){
        $columns = array();
        foreach($this->_columns() as $k=>$v)
        {
            if(isset($v['searchtype']) && $v['searchtype'])
            {
                $columns[$k] = $v['label'];
            }
        }

        $columns = array_merge(array(
            'shop_name'=>app::get('sysitem')->_('所属店铺'),
            'cat_name'=>app::get('sysitem')->_('商品类目'),
            'brand_name'=>app::get('sysitem')->_('商品品牌'),
        ),$columns);

        return $columns;
    }

    /**
     * @brief 删除商品
     * @author ajx
     * @param $params array  item_ids
     * @param $msg string 处理结果
     *
     * @return
     */
    public function doDelete($params)
    {
        //团购判断
        $params['item_id'] = $params['item_id'];
        $activityStatus = app::get('sysitem')->rpcCall('promotion.activity.item.list',$params);
        if($activityStatus['status'])
        {
            $msg = app::get('sysitem')->_('该商品正在活动中不可修改！');
            throw new \LogicException($msg);
        }
        $ojbMdlItem = app::get('sysitem')->model('item');
        $result = $ojbMdlItem->delete($params);
        if(!$result)
        {
            $msg = app::get('sysitem')->_('商品删除失败');
            throw new \logicException($msg);
        }
        return true;
    }
}
