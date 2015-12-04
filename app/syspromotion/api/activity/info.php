<?php
class syspromotion_api_activity_info{
    public $apiDescription = "获取活动详情";
    public function getParams()
    {
        $data['params'] = array(
            'activity_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'活动id'],
            'fields' => ['type'=>'field_list', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'查询字段'],
        );
        return $data;
    }
    public function getInfo($params)
    {
        $filter['activity_id'] = $params['activity_id'];

        $row = "activity_id,activity_name,activity_tag,shoptype,release_time";
        if($params['fields'])
        {
            $row = $params['fields'];
        }

        $objActivity = kernel::single('syspromotion_activity');
        $dataInfo = $objActivity->getInfo($row,$filter);
        
        return $dataInfo;
    }
}
