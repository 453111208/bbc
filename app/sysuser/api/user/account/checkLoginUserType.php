<?php
class sysuser_api_user_account_checkLoginUserType{
    public $apiDescription = "检测用户登录类型";

    public function getParams()
    {
        $params['params'] = array(
            'user_name' => ['type'=>'int','valid'=>'required', 'description'=>'登录用户名','default'=>'','example'=>''],
        );
        return $params;
    }

    public function checkType($params)
    {
        try{
            $name = $params['user_name'];
            $checkResult = kernel::single('pam_tools')->checkLoginNameType($name);
        }
        catch(\LogicException $e)
        {
            throw new \LogicException($e->getMessage());
        }
        return $checkResult;
    }
}


