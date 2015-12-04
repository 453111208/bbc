<?php

class systrade_api_tradeAbnormal_list {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取异常订单取消列表';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getparams()
    {
        $return['params'] = array(
            'role' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'调用角色'],
            'tid' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'订单ID'],
            'page_no' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'orderBy' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序，默认modified_time desc'],
            'fields'=> ['type'=>'field_list','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'需要返回的字段'],
        );

        return $return;
    }

    public function getData($params)
    {
        if( $params['role'] == 'seller' )
        {
            if($params['oauth'])
            {
                $sellerId = $params['oauth']['account_id'];
                $shopId = app::get('systrade')->rpcCall('shop.get.loginId',array('seller_id'=>$sellerId),'seller');

            }
            $filter['shop_id'] == $shopId;
        }

        $objMdlTradeAbnormal = app::get('systrade')->model('tradeabnormal');

        $filter = array();
        if( $params['tid'] )
        {
            $filter['tid'] = $params['tid'];
        }

        $countTotal = $objMdlTradeAbnormal->count($filter);

        if( $countTotal )
        {
            $pageTotal = ceil($countTotal/$params['page_size']);
            $page =  $params['page_no'] ? $params['page_no'] : 1;
            $limit = $params['page_size'] ? $params['page_size'] : 10;
            $currentPage = $pageTotal <= $page ? $totalPage : $page;
            $offset = ($currentPage-1) * $limit;

            $orderBy = $params['orderBy'] ? $params['orderBy'] : 'modified_time desc';
            $data['tradeAnormal'] = $objMdlTradeAbnormal->getList($params['fields'], $filter, $offset, $limit, $orderBy);
        }
        $data['total_results'] = $countTotal;

        return $data;
    }

}
