<?php
class sysopen_api_open_getShopConf{
    public $apiDescription = "获取商家开放平台的配置参数";
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
        $info = kernel::single('sysopen_shop_conf')->getShopConf($shopId);
        return $info;
    }
}


