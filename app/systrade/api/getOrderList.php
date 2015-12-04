<?php

class systrade_api_getOrderList {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取子订单交易列表信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'oids' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'子订单编号，如果子订单用逗号隔开'],
            'item_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'商品id(和oids至少有一个必填)'],
            'status' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'子订单状态，如果多个状态用逗号隔开'],
            'page_no' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认100条'],
            'fields'=> ['type'=>'field_list','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'获取单个子订单需要返回的字段'],
        );

        return $return;
    }

    /**
     * 获取单笔交易数据
     *
     * @param array $params 接口传入参数
     * @return array
     */
    public function getData($params)
    {
        $orderList = array();
        if($params['item_id'])
        {
            $filter['item_id'] = $params['item_id'];
        }
        if($params['oid'])
        {
            $filter['oid'] = explode(',',$params['oids']);
        }
        if($params['sku_id'])
        {
            $filter['sku_id'] = explode(',',$params['sku_id']);
        }
        if($params['status'])
        {
            $filter['status'] = explode(',',$params['status']);
        }
        if(!$filter)
        {
            throw new Exception('item_id、oid至少有一个必填');
        }

        $objMdlOrder = app::get('systrade')->model('order');
        $orderList = $objMdlOrder->getList($params['fields'], $filter );
        return $orderList;
    }
}

