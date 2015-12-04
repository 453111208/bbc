<?php
class sysuser_api_getItemCollectList {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取会员商品收藏列表';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'page_no' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1','default'=>'','example'=>''],
            'page_size' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认20条','default'=>'','example'=>''],
            'fields'=> ['type'=>'field_list','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填','default'=>'','example'=>''],
            'cat_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'商品3级分类','default'=>'','example'=>''],
            'orderBy' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序','default'=>'','example'=>''],
        );

        return $return;
    }

    public function getItemCollectList($params)
    {
        $objMdlFav = app::get('sysuser')->model('user_fav');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        if($params['cat_id'])
        {
            $filter = array('user_id'=>$params['user_id'],'cat_id'=>$params['cat_id']);
        }
        else
        {
            $filter = array('user_id'=>$params['user_id']);
        }

        $pageSize = $params['page_size'] ? $params['page_size'] : 40;
        $pageNo = $params['page_no'] ? $params['page_no'] : 1;
        $max = 1000000;
        if($pageSize >= 1 && $pageSize < 500 && $pageNo >=1 && $pageNo < 200 && $pageSize*$pageNo < $max)
        {
            $limit = $pageSize;
            $page = ($pageNo-1)*$limit;
        }

        $orderBy    = $params['orderBy'] ? $params['orderBy'] : 'gnotify_id DESC';
        $aData = $objMdlFav->getList($params['fields'], $filter, $page,$limit, $orderBy);
        $collectData = $this->__itemData($aData);
        $itemCount = $objMdlFav->getcount($filter);
        $itemData = array(
                'itemcollect' => $collectData,
                'itemcount' => $itemCount,
            );

        return $itemData;
    }

    private function __itemData($data)
    {
        foreach ($data as $key => $value)
        {
            $collectItemId[$key] = $value['item_id'];
            $collectData[$value['item_id']]['gnotify_id'] = $value['gnotify_id'];
            $collectData[$value['item_id']]['item_id'] = $value['item_id'];
            $collectData[$value['item_id']]['user_id'] = $value['user_id'];
            $collectData[$value['item_id']]['sku_id'] = $value['sku_id'];
            $collectData[$value['item_id']]['cat_id'] = $value['cat_id'];
            $collectData[$value['item_id']]['goods_name'] = $value['goods_name'];
            $collectData[$value['item_id']]['goods_price'] = $value['goods_price'];
            $collectData[$value['item_id']]['image_default_id'] = $value['image_default_id'];
            $collectData[$value['item_id']]['email'] = $value['email'];
            $collectData[$value['item_id']]['cellphone'] = $value['cellphone'];
            $collectData[$value['item_id']]['send_time'] = $value['send_time'];
            $collectData[$value['item_id']]['create_time'] = $value['create_time'];
            $collectData[$value['item_id']]['disabled'] = $value['disabled'];
            $collectData[$value['item_id']]['remark'] = $value['remark'];
            $collectData[$value['item_id']]['object_type'] = $value['object_type'];
        }
        $objMdlItem = app::get('sysitem')->model('item');
        $itemData = $objMdlItem->getList('item_id',array('item_id'=>$collectItemId));
        foreach ($itemData as $key => $value)
        {
            $itemId[$key] = $value['item_id'];
        }

        foreach ($collectItemId as $value)
        {
            if(!in_array($value, $itemId))
            {
                $unItemId[] = $value;
                $collectData[$value]['is_online'] = 'yes';
            }
        }
        return $collectData;
        //echo '<pre>';print_r($collectData);exit();
    }
}
