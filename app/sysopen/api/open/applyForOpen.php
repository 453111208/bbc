<?php
class sysopen_api_open_applyForOpen{
    public $apiDescription = "申请商户开放平台";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'店铺ID'],
        );
        return $return;
    }

    public function apply($params)
    {
        $shopId = $params['shop_id'];
        $info = kernel::single('sysopen_key')->apply($shopId);
        return $info;
    }
}


