<?php
class sysshop_api_getShopId{
    public $apiDescription = "获取指定用户的店铺id";
    public function getParams()
    {
        $return['params'] = array(
            'seller_id' => ['type'=>'int','valid'=>'','description'=>'卖家用户id','default'=>'当前登录的商家','example'=>'1'],
        );
        return $return;
    }

    public function getSellerShopId($params)
    {
        if(is_null($params['oauth']) && is_null($params['seller_id']))
        {
            throw new \LogicException('登录用户信息有误');
        }

        if(!$params['seller_id'] && $oauth)
        {
            $params['seller_id'] = $oauth['account_id'];
            unset($params['oauth']);
        }

        $shopId = shopAuth::getShopId($params['seller_id']);

        return $shopId;
    }
}


