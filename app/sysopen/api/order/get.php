<?php
class sysopen_api_order_get{
    public $apiDescription = "获取单个订单详情";
    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单所属用户id'],
            'status' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单状态'],
            'buyer_rate' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单评价状态'],
            'tid' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单编号,多个用逗号隔开'],
            'create_time_start' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'查询指定时间内的交易创建时间开始yyyy-MM-dd'],
            'create_time_end' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'查询指定时间内的交易创建时间结束yyyy-MM-dd'],

            'page_no' => ['type'=>'int','valid'=>'','description'=>'分页当前页码,1<=no<=499','example'=>'','default'=>'1'],
            'page_size' =>['type'=>'int','valid'=>'','description'=>'分页每页条数(1<=size<=200)','example'=>'','default'=>'40'],
            'order_by' => ['type'=>'int','valid'=>'','description'=>'排序方式','example'=>'','default'=>'created_time desc'],
            'fields' => ['type'=>'field_list','valid'=>'required','description'=>'获取的交易字段集','example'=>'','default'=>''],
        );
        return $return;
    }

    public function get($params, $oauth)
    {
        sysopen_oauth::checkShopOauth($oauth);

        $params['shop_id'] = $oauth['shop_id'];
        return app::get('sysopen')->rpcCall('trade.get.list', $params);
    }
}


