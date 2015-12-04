<?php
class sysshop_api_shop_get{

    public $apiDescription = "获取企业基本信息";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'企业id','default'=>'','example'=>'1'],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'企业数据字段','default'=>'shop_id,shop_name,shop_descript,shop_type,status,open_time,shop_logo,shop_area,shop_addr','example'=>'shop_id,shop_name'],
        );
        return $return;
    }

    public function get($params)
    {
        $objDataShop = kernel::single('sysshop_data_shop');
        $row ="shop_id,shop_name,shop_descript,shop_type,status,open_time,qq,wangwang,shop_logo,shop_area,shop_addr,is_shopcenter,background_img,shopuser_name,mobile,email,map_img";
        if($params['fields'])
        {
            $row = $params['fields'];
        }
        $filter = array(
            'shop_id' => $params['shop_id'],
        );
        $shopData = $objDataShop->getShopInfo($row,$filter);
        return $shopData;
    }
}
