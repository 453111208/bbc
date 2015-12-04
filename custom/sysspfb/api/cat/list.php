<?php
class syscategory_api_cat_list{
    public $apiDescription = "获取类目树形结构";
    public function getParams()
    {
        $return['params'] = array(
            'fields' => ['type'=>'field_list','valid'=>'', 'description'=>'获取类目的指定字段','default'=>'cat_name,level,cat_id','example'=>'cat_name,cat'],
        );
        return $return;
    }
    public function getList($params)
    {
        $row = "cat_id,parent_id,cat_path,level,cat_name,child_count,cat_logo";
        if($params['fields'])
        {
            $row = $params['fields'];
        }
        $row = str_append($row,'level,cat_id,parent_id');
        $objMdlCat = app::get('syscategory')->model('cat');
        $catList = $objMdlCat->getList($row);
        foreach($catList as $key=>$value)
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
