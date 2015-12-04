<?php
class sysopen_api_open_shopInfo{
    public $apiDescription = "获取商户的开发平台数据";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'店铺ID'],
        );
        return $return;
    }

    public function get($params)
    {
        $shopId = $params['shop_id'];
        $info = kernel::single('sysopen_shop_info')->getShopOpenInfo($shopId);
        return $info;
    }
}


