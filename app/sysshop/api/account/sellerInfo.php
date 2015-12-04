<?php

class sysshop_api_account_sellerInfo {

    public $apiDescription = "获取指定的子帐号信息";

    public function getParams()
    {
        $return['params'] = array(
            'seller_id' => ['type'=>'int','valid'=>'required','description'=>'账号ID','default'=>'','example'=>'1'],
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
        );

        return $return;
    }

    public function get($params)
    {
        $objMdlSeller = app::get('sysshop')->model('seller');
        $data = $objMdlSeller->getRow('*',['seller_id'=>$params['seller_id'],'shop_id'=>$params['shop_id']]);
        if( $data )
        {
            $data['login_account'] = shopAuth::getSellerName($params['seller_id']);
        }

        return $data;
    }
}

