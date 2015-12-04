<?php

class sysshop_api_account_rolesDel {

    public $apiDescription = "删除指定的商家角色";

    public function getParams()
    {
        $return['params'] = array(
            'role_id' => ['type'=>'int','valid'=>'required','description'=>'角色id','default'=>'','example'=>'1'],
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
        );

        return $return;
    }

    public function delete($params)
    {
        $objMdlRoles = app::get('sysshop')->model('roles');

        $objMdlSeller = app::get('sysshop')->model('seller');
        $data = $objMdlSeller->getRow('seller_id',['shop_id'=>$params['shop_id'],'role_id'=>$params['role_id']]);
        if( $data )
        {
            throw new \LogicException('该角色已被子账号绑定，请解绑后删除');
        }

        try
        {
            $data = $objMdlRoles->delete(['role_id'=>$params['role_id'],'shop_id'=>$params['shop_id']]);
        }
        catch( \LogicException $e )
        {
            throw new \LogicException('角色删除失败');
        }

        return $data;
    }
}

