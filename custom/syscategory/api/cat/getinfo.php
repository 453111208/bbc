<?php
class syscategory_api_cat_getinfo{
    public $apiDescription = "获取指定类目信息";
    public function getParams()
    {
        $return['params'] = array(
            'parent_id' => ['type'=>'int','valid'=>'', 'description'=>'父类id','default'=>'','example'=>'33'],
            'cat_id' => ['type'=>'string','valid'=>'', 'description'=>'类目id,和cat_name二选一','default'=>'','example'=>'33'],
            'cat_path' => ['type'=>'string','valid'=>'', 'description'=>'类目id','default'=>'','example'=>'33'],
            'cat_name' => ['type'=>'string','valid'=>'', 'description'=>'类目名称和cat_id二选一','default'=>'','example'=>'大家电'],
            'level' => ['type'=>'int','valid'=>'', 'description'=>'类目等级1、2、3,当cat_name不为空时，此参数必填','default'=>'','example'=>'1'],
            'fields' => ['type'=>'field_list','valid'=>'', 'description'=>'获取类目的指定字段','default'=>'cat_name,level,cat_id','example'=>'cat_name,cat_id'],
        );
        return $return;
    }

    public function getList($params)
    {
        $objMdlCat = app::get('syscategory')->model('cat');
        $row = "cat_id,cat_name,level,parent_id";
        if($params['fields'])
        {
            $row = $params['fields'];
            $row = str_append($row,'cat_id');
        }
        if($params['cat_id'])
        {
            $filter['cat_id'] = explode(',',$params['cat_id']);
        }

        if($params['cat_path'])
        {
            $filter['cat_path|has'] = ','.$params['cat_path'].',';
        }

        if(isset($params['parent_id']) && intval($params['parent_id']) >= 0 )
        {
            $filter['parent_id'] = $params['parent_id'];
        }
        elseif(is_array($params['parent_id']))
        {
            $filter['parent_id'] = explode(',',$params['parent_id']);
        }

        if($params['cat_name'] && $params['level'])
        {
            $filter['cat_name'] = $params['cat_name'];
            $filter['level'] = $params['level'];
        }

        if($params['level'])
        {
            $filter['level'] = $params['level'];
        }
        $catList = $objMdlCat->getList($row,$filter);
        return array_bind_key($catList,'cat_id');
    }
}
