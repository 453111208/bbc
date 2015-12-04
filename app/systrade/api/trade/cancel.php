<?php
class systrade_api_trade_cancel{
    public $apiDescription = "交易取消";
    public function getParams()
    {
        $return['params'] = array(
            'tid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单id'],
            'cancel_reason' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单取消原因'],
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单所属用户id'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单所属店铺id'],
        );
        return $return;
    }
    public function cancelTrade($params)
    {
        if($params['oauth']['account_id'] && $params['oauth']['auth_type'] == "member" )
        {
            $params['user_id'] = $params['oauth']['account_id'];
        }
        elseif($params['oauth']['account_id'] && $params['oauth']['auth_type'] == "shop")
        {
            $sellerId = $params['oauth']['account_id'];
            $params['shop_id'] = app::get('systrade')->rpcCall('shop.get.loginId',array('seller_id'=>$sellerId),'seller');
        }

        $postData = array(
            'filter' => array(
                'tid' => $params['tid'],
            ),
            'data' => array(
                'status' => 'TRADE_CLOSED_BY_SYSTEM',
                'end_time' => time(),
                'cancel_reason' => $params['cancel_reason'],
            ),
        );
        if($params['user_id'])
        {
            $postData['filter']['user_id'] = $params['user_id'];
        }
        if($params['shop_id'])
        {
            $postData['filter']['shop_id'] = $params['shop_id'];
        }

        if($params['oauth']['account_id'])
        {
            $postData['operator']['op_id'] = $params['oauth']['account_id'];
            $postData['operator']['op_account'] = $params['oauth']['account_name'];
            $postData['operator']['account_type'] = $params['oauth']['auth_type'];
        }

        try
        {
            kernel::single('systrade_data_trade_cancel')->generate($postData);
        }
        catch(Exception $e)
        {
            throw $e;
        }
        return true;
    }
}
