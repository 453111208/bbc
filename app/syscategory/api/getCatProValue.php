<?php
class syscategory_api_getCatProValue {

    /**
     * 接口作用说明
     */
    public $apiDescription = '根据类目id获取类目关联的属性值';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'cat_id' => ['type'=>'int','valid'=>'required', 'description'=>'3级类目id','default'=>'','example'=>''],
            'type' => ['type'=>'int','valid'=>'required', 'description'=>'属性类型（nature,spec）','default'=>'','example'=>''],
            'prop_id' => ['type'=>'string','valid'=>'', 'description'=>'属性id集合，多个用逗号隔开','default'=>'','example'=>''],
        );

        return $return;
    }

    public function getCatProValue($params)
    {
        $objMdlCat = app::get('syscategory')->model('cat');

        $filter['cat_id'] = $params['cat_id'];
        if($filter['cat_id'])
        {
            $cat = $objMdlCat->getRow('level,is_leaf',$filter);
            if($cat['level'] != 3)
            {
                throw new LogicException('cat_id必须为三级类目的id');
            }
        }
        $propIds = explode(',',$params['prop_id']);
        if(!$params['prop_id'])
        {
            $objMdlCatRelProp = app::get('syscategory')->model('cat_rel_prop');
            $catRelPropList = $objMdlCatRelProp->getList('*',$filter,0,-1,'order_sort ASC');
            if( !$catRelPropList ) return array();
            $propIds = array_column($catRelPropList,'prop_id');
        }

        $objMdlProps = app::get('syscategory')->model('props');
        $props = $objMdlProps->getList('*',array('prop_id'=>$propIds, 'prop_type'=>$params['type']),0,-1,'order_sort ASC');
        $props = array_bind_key($props,'prop_id');

        $objMdlPropValues = app::get('syscategory')->model('prop_values');
        $propValues = $objMdlPropValues->getList('*',array('prop_id'=>$propIds));
        foreach($propValues as $key=>$val)
        {
            $newPropValues[$val['prop_id']][$val['prop_value_id']] = $val;
        }

        foreach($props as $key=>$value)
        {
            $props[$key]['prop_value'] = $newPropValues[$value['prop_id']];
        }
        return $props;
    }
}
