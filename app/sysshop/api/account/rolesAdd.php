<?php

class sysshop_api_account_rolesAdd {

    public $apiDescription = "创建商家角色";

    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
            'role_name' => ['type'=>'string','valid'=>'required','description'=>'角色名称','default'=>'','example'=>'商品客服'],
            'workground' => ['type'=>'string','valid'=>'required','description'=>'角色拥有的权限ID','default'=>'','example'=>'item,showTrade'],
        );

        return $return;
    }

    public function save($params)
    {
        $objMdlRoles = app::get('sysshop')->model('roles');
        $roleId = $objMdlRoles->getRow('role_id',['shop_id'=>$params['shop_id'],'role_name'=>$params['role_name']]);
        if( $roleId )
        {
            throw new \LogicException('角色名称已被使用');
        }

        return $objMdlRoles->insert($params);
    }
}

