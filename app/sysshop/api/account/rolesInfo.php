<?php

class sysshop_api_account_rolesInfo {

    public $apiDescription = "获取指定的商家角色信息";

    public function getParams()
    {
        $return['params'] = array(
            'role_id' => ['type'=>'int','valid'=>'required','description'=>'角色id','default'=>'','example'=>'1'],
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
        );

        return $return;
    }

    public function get($params)
    {
        $objMdlRoles = app::get('sysshop')->model('roles');
        $data = $objMdlRoles->getRow('*',['role_id'=>$params['role_id'],'shop_id'=>$params['shop_id']]);
        return $data;
    }
}

