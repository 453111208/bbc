<?php
class syspromotion_api_activity_itemList{
    public $apiDescription = "获取参加活动的商品";
    public function getParams()
    {
        $data['params'] = array(
            'id' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'主键id'],
            'activity_id' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'活动id,多个用“,”隔开'],
            'cat_id' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'类目id,多个用“,”隔开'],
            'item_id' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'商品id，多个用“,”隔开'],
            'status' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'活动状态'],
            'start_time' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'sthan', 'description'=>'与开始时间相比，大于或小于指定时间,值为(sthan、bthan)'],
            'end_time' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'bthan', 'description'=>'与开结束相比，大于或小于指定时间,值为(sthan、bthan)'],
            'time' => ['type'=>'string', 'valid'=>'date', 'default'=>'', 'example'=>'2015-14-04 20:30', 'description'=>'指定时间(2015-14-04)'],
            'shop_id' => ['type'=>'int', 'valid'=>'int', 'default'=>'', 'example'=>'', 'description'=>'店铺id'],

            'page_no' => ['type'=>'int','valid'=>'int','description'=>'分页当前页码,1<=no<=499','example'=>'','default'=>'1'],
            'page_size' =>['type'=>'int','valid'=>'int','description'=>'分页每页条数(1<=size<=200)','example'=>'','default'=>'40'],
            'order_by' => ['type'=>'int','valid'=>'string','description'=>'排序方式','example'=>'','default'=>'item_id desc'],
            'fields' => ['type'=>'field_list', 'valid'=>'string', 'default'=>'activity_name', 'example'=>'', 'description'=>'查询字段'],
        );
        return $data;
    }
    public function getList($params)
    {
        $row = $params['fields'] ? $params['fields'] :"*";
        $filter = array();

        $columnIds = ['id','activity_id','cat_id','item_id'];

        foreach( $columnIds as $id )
        {
            if($params[$id])
            {
                $filter[$id] = explode(',',$params[$id]);
            }
        }

        if($params['shop_id']!='')
        {
            $filter['shop_id'] = $params['shop_id'];
        }

        if($params['status'])
        {
            $filter['verify_status'] = $params['status'];
        }

        if($params['start_time'])
        {
            $filter['start_time|'.$params['start_time']] = $params['time'] ? strtotime($params['time']) : time();
        }

        if($params['end_time'])
        {
            $filter['end_time|'.$params['end_time']] = $params['time'] ? strtotime($params['time']) : time();
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
            $orderBy = "item_id desc";
        }

        $objActivityItem = kernel::single('syspromotion_activity');
        $data['list'] = $objActivityItem->getItemList($row,$filter,$page,$limit,$orderBy);
        $data['count'] = $objActivityItem->countActivityItem($filter);
        $data['status'] = $objActivityItem->getActivityStatus($params);
        //echo '<pre>';print_r($data);exit();
        return $data;
    }
}
