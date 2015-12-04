<?php
class syspromotion_api_activity_list{
    public $apiDescription = "获取活动列表";
    public function getParams()
    {
        $data['params'] = array(
            'activity_id' => ['type'=>'string', 'valid'=>'|string', 'default'=>'', 'example'=>'', 'description'=>'活动id'],
            'activity_name' => ['type'=>'string', 'valid'=>'|string', 'default'=>'', 'example'=>'', 'description'=>'活动名称'],
            'activity_tag' => ['type'=>'string', 'valid'=>'|string', 'default'=>'', 'example'=>'', 'description'=>'活动标签'],
            'release_time' => ['type'=>'string', 'valid'=>'', 'default'=>'agree', 'example'=>'sthan', 'description'=>'与发布时间相比，大于或小于指定时间,值为(sthan、bthan)'],
            'end_time' => ['type'=>'string', 'valid'=>'', 'default'=>'agree', 'example'=>'bthan', 'description'=>'与开结束相比，大于或小于指定时间,值为(sthan、bthan)'],
            'time' => ['type'=>'string', 'valid'=>'|date', 'default'=>'agree', 'example'=>'2015-14-04 20:30', 'description'=>'指定时间(2015-14-04)'],


            'page_no' => ['type'=>'int','valid'=>'|int','description'=>'分页当前页码,1<=no<=499','example'=>'','default'=>'1'],
            'page_size' =>['type'=>'int','valid'=>'|int','description'=>'分页每页条数(1<=size<=200)','example'=>'','default'=>'40'],
            'order_by' => ['type'=>'int','valid'=>'|string','description'=>'排序方式','example'=>'','default'=>'created_time desc'],
            'fields' => ['type'=>'field_list', 'valid'=>'', 'default'=>'activity_name', 'example'=>'', 'description'=>'查询字段'],
        );
        return $data;
    }
    public function getList($params)
    {
        if($params['activity_id'])
        {
            $filter['activity_id'] = explode(',',$params['activity_id']);
        }

        if($params['activity_name'])
        {
            $filter['activity_name'] = $params['activity_name'];
        }

        if($params['activity_tag'])
        {
            $filter['activity_tag'] = $params['activity_tag'];
        }

        if($params['release_time'])
        {
            $filter['release_time|'.$params['release_time']] = $params['time'] ? strtotime($params['time']) : time();
        }

        if($params['end_time'])
        {
            $filter['end_time|'.$params['end_time']] = $params['time'] ? strtotime($params['time']) : time();
        }

        $row = "activity_id,activity_name,activity_tag,shoptype,release_time";
        if($params['fields'])
        {
            $row = $params['fields'];
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
            $orderBy = "created_time desc";
        }
        $objActivity = kernel::single('syspromotion_activity');
        $datalist = $objActivity->getList($row,$filter,$page,$limit,$orderBy);

        $activityCount = $objActivity->countActivity($filter);
        $result = array(
            'data' => $datalist,
            'count' => $activityCount,
        );

        return $result;
    }
}
