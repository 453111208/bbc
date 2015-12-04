<?php
class sysopen_api_oauth_login{
    public $apiDescription = "用于OAuth登陆的接口";
    public function getParams()
    {
        $return['params'] = array(
            'loginname' => ['type'=>'string','valid'=>'','description'=>'用户名','default'=>'','example'=>'shopex01'],
            'password' => ['type'=>'string','valid'=>'','description'=>'用户密码','default'=>'','example'=>'demo123'],
            'oauth_type' => ['type'=>'string','valid'=>'','description'=>'用户的类型','default'=>'','example'=>'seller'],
        );
        return $return;
    }

    public function login($params, $oauth, $appInfo)
    {
        $type = $params['oauth_type'] ? $params['oauth_type'] : 'seller';

        $username = $params['loginname'];
        $password = $params['password'];

        switch($type)
        {
            case 'seller':
                $userinfo = app::get('sysopen')->rpcCall('account.shop.oauth.login', ['loginname' => $username, 'password'=>$password]);
                $userinfo['data']['accountid'] = $userinfo['data']['sellerId'];
                $userinfo['data']['shop_id'] =  app::get('sysopen')->rpcCall('shop.get.loginId', [ 'seller_id' => $userinfo['data']['sellerId'] ]);

                //checkout key和develop模式
                kernel::single('sysopen_shop_check')->checkLogin($userinfo['data']['shop_id'], $appInfo['client_id']);

                sysopen_oauth::checkShopLogin($userinfo);
                break;
            case 'costomer':
            case 'admin':
                throw new LogicException('不支持的用户类型!当前仅支持seller。');
                break;
        }
        return $userinfo;
    }
}


