<?php
class sysuser_api_user_account_checkLoginPwd{
    public $apiDescription = "检测用户登录密码";
    public function getParams()
    {
        $params['params'] = array(
            'password' => ['type'=>'int','valid'=>'required', 'description'=>'登录用户密码','default'=>'','example'=>''],
        );
        return $params;
    }
    public function checkPwd($params)
    {
        $oauth = $params['oauth'];
        if(!$oauth || $oauth['auth_type'] != "member")
        {
            throw new \LogicException('用户登录异常');
        }

        $accountId = $oauth['account_id'];
        $userName = $oauth['account_name'];

        try
        {
            kernel::single('sysuser_passport')->checkLoginPassword($accountId, $params['password']);
        }
        catch(\LogicException $e)
        {
            throw new \LogicException($e->getMessage());
        }
        return true;
    }

}
