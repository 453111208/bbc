<?php
class syscategory_api_getPropList{
    public $apiDescription = "获取属性列表";
    public function getParams()
    {
        $params['params'] = array(
            'prop_id' => ['type'=>'string','valid'=>'required', 'description'=>'属性id集合，多个用逗号隔开','default'=>'','example'=>''],
        );
        return $params;
    }
    public function getList($params)
    {
        $prop_id = explode(',',$params['prop_id']);
        $objMdlProps = app::get('syscategory')->model('props');
        $props = $objMdlProps->getList('*',array('prop_id' => $prop_id));
        $props = array_bind_key($props,'prop_id');
        return $props;
    }
}
