<?php
class sysuser_api_user_account_logout{

    public $apiDescription = "用户登录";
    public function getParams()
    {
        $params['params'] = array(
            'user_name' => ['type'=>'int','valid'=>'required', 'description'=>'登录用户名','default'=>'','example'=>''],
            'password' => ['type'=>'int','valid'=>'required', 'description'=>'用户登录密码','default'=>'','example'=>''],
        );
        return $params;
    }
    
    public function userLogout($params)
    {
        return true;
    }
}
