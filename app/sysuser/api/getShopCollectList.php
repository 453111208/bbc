<?php
class sysuser_api_getShopCollectList {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取会员店铺收藏列表';

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
            'orderBy' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序','default'=>'','example'=>''],
        );

        return $return;
    }

    public function getShopCollectList($params)
    {
        $objMdlFav = app::get('sysuser')->model('shop_fav');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $filter = array('user_id'=>$params['user_id']);

        $pageSize = $params['page_size'] ? $params['page_size'] : 40;
        $pageNo = $params['page_no'] ? $params['page_no'] : 1;
        $max = 1000000;
        if($pageSize >= 1 && $pageSize < 500 && $pageNo >=1 && $pageNo < 200 && $pageSize*$pageNo < $max)
        {
            $limit = $pageSize;
            $page = ($pageNo-1)*$limit;
        }

        $orderBy    = $params['orderBy'] ? $params['orderBy'] : 'snotify_id DESC';
        $aData = $objMdlFav->getList($params['fields'], $filter, $page,$limit, $orderBy);
        $shopCount = $objMdlFav->getcount($filter);
        $shopData = array(
                'shopcollect' => $aData,
                'shopcount' => $shopCount,
            );
        //echo '<pre>';print_r($itemCount);exit();
        return $shopData;
    }
}
