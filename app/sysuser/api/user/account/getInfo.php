<?php
class sysuser_api_user_account_getInfo{

    public $apiDescription = "获取用户登录信息";
    public function getParams()
    {
        $return['params'] = array(
            'user_name' => ['type'=>'int',
                             'valid'=>'required',
                             'description'=>'登录用户名',
                             'default'=>'',
                             'example'=>''],
        );
        return $return;
    }

    public function get($params)
    {
        $fields = "user_id,mobile,email";
        $loginName = $params['user_name'];
        $loginType = kernel::single('pam_tools')->checkLoginNameType($loginName);
        $filter = [$loginType => $loginName];
        
        
        if ($account = app::get('sysuser')->model('account')->getRow($fields, $filter))
        {
            $userId = $account['user_id'];
            if ($user = app::get('sysuser')->model('user')->getRow('email_verify',array('user_id'=>$userId)))
            {
                $account['email_verify'] = $user['email_verify'];
            }
            return $account;
        }
        return [];
    }
}
