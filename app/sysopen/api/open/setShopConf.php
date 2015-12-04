<?php
class sysopen_api_open_setShopConf{
    public $apiDescription = "设置商家开放平台的配置参数";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'店铺ID'],
            'developMode' => ['type'=>'string', 'valid'=>'', 'default'=>'false', 'example'=>'true','description'=>'开发者模式或者是编辑模式,DEVELOP或者false'],
        );
        return $return;
    }

    public function set($params)
    {
        $shopId = $params['shop_id'];
        $developMode = $params['developMode'];
        $info = kernel::single('sysopen_shop_conf')->setShopConf($shopId, $developMode);
        return $info;
    }
}


