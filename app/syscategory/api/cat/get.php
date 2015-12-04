<?php
class syscategory_api_cat_get{
    public $apiDescription = "获取指定一级类目的信息以及2、3级类目信息";
    public function getParams()
    {
        $return['params'] = array(
            'cat_id' => ['type'=>'string','valid'=>'required', 'description'=>'类目id,以逗号相隔的多个id数据','default'=>'','example'=>'1,23'],
            'fields' => ['type'=>'field_list','valid'=>'', 'description'=>'获取类目的指定字段','default'=>'cat_name,level,cat_id','example'=>'cat_name,cat_id'],
        );
        return $return;
    }
    public function getList($params)
    {
        $catIds = explode(',',$params['cat_id']);
        $row = "cat_id,parent_id,cat_path,level,cat_name,child_count";
        if($params['fields'])
        {
            $row = $params['fields'];
            $row = str_append($row,'level,parent_id');
        }

        $db = app::get('syscategory')->database();
        $data = array();
        foreach($catIds as $catId){
            $cat = $db->executeQuery('SELECT '.$row.' FROM syscategory_cat WHERE cat_id='.$catId.' or cat_path LIKE "%,'.$catId.',%"');
            foreach($cat as $value)
            {
                if($value['level'] == '1')
                {
                    $data[$value['cat_id']] = $value;
                }
                elseif($value['level'] == '2')
                {
                    $lv2[$value['parent_id']][$value['cat_id']] = $value;
                }
                elseif($value['level'] == '3')
                {
                    $lv3[$value['parent_id']][$value['cat_id']] = $value;
                }
            }
        }

        if(!$data)
        {
            throw new \LogicException('参数cat_id必须为一级类目id');
        }

        foreach($data as $catId => $val)
        {
            foreach($lv2[$catId] as $k=>$v)
            {
                $lv2[$catId][$k]['lv3'] = $lv3[$k];

            }
            $data[$catId]['lv2'] = $lv2[$catId];
        }
        return $data;
    }
}


