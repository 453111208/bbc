<?php
class sysshop_api_shop_search{

    public $apiDescription = "根据店铺名称查询店铺列表数据";
    public function getParams()
    {
        $return['params'] = array(
            'shop_name' => ['type'=>'string','valid'=>'required','description'=>'店铺名称关键字','default'=>'','example'=>'1'],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'店铺数据字段','default'=>'shop_id,shop_name,shop_descript,shop_type,status,open_time,shop_logo,shop_area,shop_addr','example'=>'shop_id,shop_name'],
        );
        return $return;
    }
    public function getList($params)
    {
        $objDataShop = kernel::single('sysshop_data_shop');
        $row ="shop_id,shop_name,shop_descript,shop_type,status,open_time,shop_logo,shop_area,shop_addr";

        if($params['fields'])
        {
            $row = $params['fields'];
        }

        $filter = array(
            'shop_name|has' => $params['shop_name'],
        );
        $shopData = $objDataShop->getShopInfo($row,$filter,false);
        return $shopData;
    }
}
