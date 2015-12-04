<?php
class syscategory_api_brand_getList{
    public $apiDescription = "获取品牌列表";
    public function getParams()
    {
        $return['params'] = array(
            'brand_id' => ['type'=>'string','valid'=>'', 'description'=>'品牌id,多个id以逗号相隔','example'=>'1,2,3','default'=>''],
            'brand_name' =>['type'=>'string','valid'=>'', 'description'=>'品牌名称，多个以逗号隔开','example'=>'apple,优衣库','default'=>''],
            'fields' => ['type'=>'string','valid'=>'', 'description'=>'品牌字段，多个以逗号隔开','example'=>'brand_id,brand_name','default'=>'*'],
        );
        return $return;
    }
    public function get($params)
    {
        if(!$params['brand_id'] && !$params['brand_name'])
        {
            throw new \LogicException(app::get('syscategory')->_('id 和name 至少一项必填'));
        }
        if($params['brand_id'])
        {
            $filter['brand_id'] = explode(',',$params['brand_id']);
        }
        if($params['brand_name'])
        {
            $filter['brand_name'] = explode(',',$params['brand_name']);
        }

        $rows = "brand_name,brand_id,brand_alias,order_sort,brand_logo,brand_desc";
        if($params['fields'])
        {
            $rows = $params['fields'];
            $rows = str_append($rows,'brand_id');
        }

        $objMdlBrand = app::get('syscategory')->model('brand');
        $brandList = $objMdlBrand->getList($rows,$filter);
        return array_bind_key($brandList,'brand_id');
    }
}
