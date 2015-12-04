<?php
class sysshop_api_shop_detail{

    public $apiDescription = "获取店铺详细信息";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'店铺数据字段','default'=>'shop_id,shop_name,shop_descript,shop_type,status,open_time,shop_logo,shop_area,shop_addr','example'=>'shop_id,shop_name'],
        );
        $return['extendsFields'] = ['type','cat','brand','info'];
        return $return;
    }
    public function getList($params)
    {
        $shopId = $params['shop_id'];
        $row = $params['fields'] ['rows'];
        $extends = $params['fields']['extends'];
        $objDataShop = kernel::single('sysshop_data_shop');
        $shopData = $objDataShop->getShopDetail($shopId,$row,$extends);
        return $shopData;
    }
}
