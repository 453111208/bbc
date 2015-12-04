<?php
class sysshop_api_oauth_sellerLogin{
    public $apiDescription = "用于OAuth登陆商家的接口";
    public function getParams()
    {
        $return['params'] = array(
            'loginname' => ['type'=>'string','valid'=>'','description'=>'卖家用户id','default'=>'当前登录的商家','example'=>'1'],
            'password' => ['type'=>'string','valid'=>'','description'=>'卖家用户id','default'=>'当前登录的商家','example'=>'1'],
        );
        return $return;
    }

    public function login($params)
    {
        $return = [
            'status' => 'success',
            'data' => shopAuth::apiLogin($params['loginname'], $params['password']),
            ];
        $return['data']['sellerId'] = $return['data']['sellerId'];
        return $return;
    }
}


