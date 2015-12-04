<?php
class systrade_api_trade_listByShop{
    public $apiDescription = "获取订单列表";
    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单所属用户id'],
            'status' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单状态'],
            'buyer_rate' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单评价状态'],
            'tid' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单编号,多个用逗号隔开'],
            'update_time_start' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'查询指定时间内的交易创建时间开始yyyy-MM-dd'],
            'update_time_end' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'查询指定时间内的交易创建时间结束yyyy-MM-dd'],
            'page_no' => ['type'=>'int','valid'=>'','description'=>'分页当前页码,1<=no<=499','example'=>'','default'=>'1'],
            'page_size' =>['type'=>'int','valid'=>'','description'=>'分页每页条数(1<=size<=200)','example'=>'','default'=>'40'],
            'order_by' => ['type'=>'int','valid'=>'','description'=>'排序方式','example'=>'','default'=>'created_time desc'],
            'fields' => ['type'=>'field_list','valid'=>'required','description'=>'获取的交易字段集','example'=>'','default'=>''],
        );
        $return['extendsFields'] = ['order'];
        return $return;
    }
    public function tradeList($params, $oauth)
    {

        if(isset($oauth['shop_id']))
        {
            $params['shop_id'] = $oauth['shop_id'];
        }
        else
        {
            if($params['oauth']['account_id'] && $params['oauth']['auth_type'] == "member" )
            {
                $params['user_id'] = $params['oauth']['account_id'];
            }
            elseif($params['oauth']['account_id'] && $params['oauth']['auth_type'] == "shop")
            {
                $sellerId = $params['oauth']['account_id'];
                $params['shop_id'] = app::get('systrade')->rpcCall('shop.get.loginId',array('seller_id'=>$sellerId),'seller');
            }

        }

        $tradeRow = $params['fields']['rows'];
        $orderRow = $params['fields']['extends']['order'];


        //lastmodify的范围划分
        if($params['update_time_start'] > 0 && $params['update_time_end'] > 0)
        {
            $params['modified_time|between'] = array($params['update_time_start'], $params['update_time_end']);
        }
        unset($params['update_time_start']);
        unset($params['update_time_end']);

        //分页使用
        $pageSize = $params['page_size'] ? $params['page_size'] : 40;
        $pageNo = $params['page_no'] ? $params['page_no'] : 1;
        $max = 1000000;
        if($pageSize >= 1 && $pageSize < 500 && $pageNo >=1 && $pageNo < 200 && $pageSize*$pageNo < $max)
        {
            $limit = $pageSize;
            $page = ($pageNo-1)*$limit;
        }

        $orderBy = $params['orderBy'];
        if(!$params['orderBy'])
        {
            $orderBy = "created_time desc";
        }
        unset($params['fields'],$params['page_no'],$params['page_size'],$params['order_by'],$params['oauth']);

        foreach($params as $k=>$val)
        {
            if(!$val)
            {
                unset($params[$k]);
                continue;
            }
            if($k == "status" || $k == "tid")
            {
                $params[$k] = explode(',',$val);
            }
        }

        $objMdlTrade = app::get('systrade')->model('trade');
        $count = $objMdlTrade->count($params);
        $tradeLists = $objMdlTrade->getList($tradeRow,$params,$page,$limit,$orderBy);
        $tradeLists = array_bind_key($tradeLists,'tid');
        if($orderRow && $tradeLists)
        {
            $orderRow = str_append($orderRow,'tid');
            $objMdlOrder = app::get('systrade')->model('order');
            $tids = array_column($tradeLists,'tid');
            $orderLists = $objMdlOrder->getList($orderRow,array('tid'=>$tids));
            foreach($orderLists as $key=>$value)
            {
                $tradeLists[$value['tid']]['order'][] = $value;
            }
        }

        $trade['list'] = $tradeLists;
        $trade['count'] = $count;
        return $trade;
    }
}



