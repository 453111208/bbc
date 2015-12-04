<?php
class syspromotion_api_activity_registerList{
    public $apiDescription = "获取活动报名列表";
    public function getParams()
    {
        $data['params'] = array(
            'activity_id' => ['type'=>'int', 'valid'=>'int', 'default'=>'', 'example'=>'', 'description'=>'活动id'],
            'shop_id' => ['type'=>'int', 'valid'=>'int', 'default'=>'', 'example'=>'', 'description'=>'店铺id'],
            'valid_status' => ['type'=>'int', 'valid'=>'int', 'default'=>'1', 'example'=>'', 'description'=>'有效状态'],
            'page_no' => ['type'=>'int','valid'=>'int','description'=>'分页当前页码,1<=no<=499','example'=>'','default'=>'1'],
            'page_size' =>['type'=>'int','valid'=>'int','description'=>'分页每页条数(1<=size<=200)','example'=>'','default'=>'10'],
            'order_by' => ['type'=>'int','valid'=>'string','description'=>'排序方式','example'=>'','default'=>'modified_time desc'],
            'fields' => ['type'=>'field_list', 'valid'=>'', 'default'=>'activity_name', 'example'=>'', 'description'=>'查询字段'],
        );
        return $data;
    }
    public function registerList($params)
    {
        $objMdlActivityRegister = app::get('syspromotion')->model('activity_register');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $filter = array('shop_id' => $params['shop_id']);
        $filter['valid_status'] = isset($params['valid_status']) ? $params['valid_status'] : 1;
        if($params['activity_id']!='')
        {
            $filter['activity_id'] = $params['activity_id'];
        }


        //分页使用
        $pageSize = $params['page_size'] ? $params['page_size'] : 40;
        $pageNo = $params['page_no'] ? $params['page_no'] : 1;
        $max = 1000000;
        if($pageSize >= 1 && $pageSize < 500 && $pageNo >=1 && $pageSize*$pageNo < $max)
        {
            $limit = $pageSize;
            $page = ($pageNo-1)*$limit;
        }

        $orderBy = $params['order_by'];
        if(!$params['order_by'])
        {
            $orderBy = "modified_time desc";
        }

        $registerListData = $objMdlActivityRegister->getList($params['fields'], $filter, $page, $limit, $orderBy);
        $registerListCount = $objMdlActivityRegister->count($filter);
        $result = array(
            'data' => $registerListData,
            'count' => $registerListCount,
        );

        return $result;
    }
}
