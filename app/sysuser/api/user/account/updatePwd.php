<?php
class sysuser_api_user_account_updatePwd{

    public $apiDescription = "用户密码找回和修改";
    public function getParams()
    {
        $return['params'] = array(
            'type' => ['type'=>'int','valid'=>'required', 'description'=>'类型形式(密码重置[reset]、密码修改[update])必填','default'=>'update','example'=>'reset'],
            'new_pwd' => ['type'=>'int','valid'=>'required', 'description'=>'新的密码6-20个字符，必填','default'=>'','example'=>''],
            'confirm_pwd' => ['type'=>'int','valid'=>'required', 'description'=>'新密码确认, 必填','default'=>'','example'=>''],
            'user_id' => ['type'=>'int','valid'=>'', 'description'=>'用户名id','default'=>'null','example'=>''],
            'old_pwd' => ['type'=>'int','valid'=>'', 'description'=>'原有密码(当类型形式type的值为update时，此值必填)','default'=>'null','example'=>''],
            'uname' => ['type'=>'int','valid'=>'', 'description'=>'用户名','default'=>'null','example'=>''],
        );
        return $return;
    }

    public function passwordUpdate($params)
    {
        if($params['oauth']['account_id'])
        {
            $params['user_id'] = $params['oauth']['account_id'];
        }

        try
        {
            if(!$params['user_id'])
            {
                throw new LogicException("用户信息有误");
            }

            if($params['type'] == "update" && !$params['old_pwd'])
            {
                throw new LogicException("修改密码，原始密码必填");
            }
            kernel::single('sysuser_passport')->modifyPwd($params);
        }
        catch(\LogicException $e)
        {
            throw new LogicException($e->getMessage());
        }
        return true;
    }
}
