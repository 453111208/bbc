<?php
class sysshop_api_getShopCat{

    public $apiDescription = "获取店铺自有类目";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
            'cat_id' => ['type'=>'string','valid'=>'','description'=>'id','default'=>'','example'=>'1'],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'店铺类目字段','default'=>'','example'=>'shop_id,cat_name'],
        );
        return $return;
    }
    public function getShopCat($params)
    {
        $filter['shop_id'] = $params['shop_id'];
        if($params['cat_id'])
        {
            $filter['cat_id'] = explode(',',$params['cat_id']) ;
        }
        $rows = $params['fields'] ? $params['fields'] : "*";
        $objLibShopCat = kernel::single('sysshop_data_cat');
        $shopCatList = $objLibShopCat->fetchShopCat($rows,$filter);
        return $shopCatList;
    }
}
