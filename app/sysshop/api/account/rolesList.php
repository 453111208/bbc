<?php

class sysshop_api_account_rolesList {

    public $apiDescription = "获取指定的商家角色列表";

    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
        );

        return $return;
    }

    public function get($params)
    {
        $objMdlRoles = app::get('sysshop')->model('roles');
        $data = $objMdlRoles->getList('*',['shop_id'=>$params['shop_id']]);
        return $data;
    }
}

