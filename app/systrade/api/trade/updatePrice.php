<?php
class systrade_api_trade_updatePrice{
    public $apiDescription = "交易改价";
    public function getParams()
    {
        $return['params'] = array(
            'tid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单id'],
            'post_fee' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'调整后的运费'],
            'order' => ['type'=>'json', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'子订单被调整的信息,json格式'],
        );
        return $return;
    }
    public function tradePriceUpdate($params)
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
        if($params['order'])
        {
            $params['order'] = json_decode($params['order'],true);
        }

        if($params['oauth']['account_id'])
        {
            $postData['operator']['op_id'] = $params['oauth']['account_id'];
            $postData['operator']['op_account'] = $params['oauth']['account_name'];
            $postData['operator']['account_type'] = $params['oauth']['auth_type'];
        }

        try{
            kernel::single('systrade_data_trade_editprice')->generate($params);
        }
        catch(Exception $e)
        {
            throw $e;
        }
        return true;
    }
}
