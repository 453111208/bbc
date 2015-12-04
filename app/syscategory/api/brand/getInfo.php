<?php
class syscategory_api_brand_getInfo{
    public $apiDescription = "获取品牌详情";
    public function getParams()
    {
        $return['params'] = array(
            'brand_id' => ['type'=>'string','valid'=>'', 'description'=>'品牌id,多个id以逗号相隔','example'=>'1,2,3','default'=>''],
            'fields' => ['type'=>'string','valid'=>'', 'description'=>'品牌字段，多个以逗号隔开','example'=>'brand_id,brand_name','default'=>'*'],
        );
        return $return;
    }
    public function get($params)
    {
        if(!$params['brand_id'])
        {
            throw new \LogicException(app::get('syscategory')->_('id必填'));
        }
        if($params['brand_id'])
        {
            $filter['brand_id'] = $params['brand_id'];
        }
        $rows = "brand_name";
        if($params['fields'])
        {
            $rows = $params['fields'];
        }

        $objMdlBrand = app::get('syscategory')->model('brand');
        $brand = $objMdlBrand->getRow($rows,$filter);
        return $brand;
    }
}
