<?php

class sysshop_api_account_rolesUpdate {

    public $apiDescription = "修改商家角色";

    public function getParams()
    {
        $return['params'] = array(
            'role_id' => ['type'=>'int','valid'=>'required','description'=>'角色id','default'=>'','example'=>'1'],
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
            'role_name' => ['type'=>'string','valid'=>'required','description'=>'角色名称','default'=>'','example'=>'商品客服'],
            'workground' => ['type'=>'string','valid'=>'required','description'=>'角色拥有的权限ID','default'=>'','example'=>'item,showTrade'],
        );

        return $return;
    }

    public function update($params)
    {
        $objMdlRoles = app::get('sysshop')->model('roles');
        $data = $objMdlRoles->getRow('role_id',['shop_id'=>$params['shop_id'],'role_name'=>$params['role_name']]);
        if( $data['role_id'] && $data['role_id'] != $params['role_id']  )
        {
            throw new \LogicException('角色名称已被使用');
        }

        try
        {
            $objMdlRoles->update(['role_name'=>$params['role_name'],'workground'=>$params['workground']], ['role_id'=>$params['role_id'],'shop_id'=>$params['shop_id']]);
        }
        catch( \LogicException $e )
        {
            throw new \LogicException('角色名称已被使用');
        }

        return true;
    }
}

