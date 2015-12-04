<?php

class sysshop_api_account_sellerList {

    public $apiDescription = "获取指定的商家子帐号列表";

    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
        );

        return $return;
    }

    public function get($params)
    {
        $objMdlSeller = app::get('sysshop')->model('seller');
        $data = $objMdlSeller->getList('*',['shop_id'=>$params['shop_id'],'seller_type'=>'1']);
        if( empty($data) ) return array();

        $sellerIds = array_column($data, 'seller_id');
        $accountData = app::get('sysshop')->model('account')->getList('seller_id,login_account,disabled', ['seller_id'=>$sellerIds]);
        $data = array_bind_key($data, 'seller_id');
        foreach( $accountData as $row )
        {
            if( $data[$row['seller_id']] )
            {
                $data[$row['seller_id']]['login_account'] = $row['login_account'];
                $data[$row['seller_id']]['disabled'] = $row['disabled'];
            }
        }

        return $data;
    }
}

