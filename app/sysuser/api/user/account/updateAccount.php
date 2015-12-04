<?php
class sysuser_api_user_account_updateAccount{

    public $apiDescription = "更改用户登录信息";
    public function getParams()
    {
        $params['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'description'=>'用户名id','default'=>'null','example'=>''],
            'user_name' => ['type'=>'int','valid'=>'required', 'description'=>'用户名','default'=>'null','example'=>''],
            'type' => ['type'=>'string','valid'=>'', 'description'=>'是解绑还是绑定','default'=>'null','example'=>''],
        );
        return $params;
    }
    
    public function accountUpdate($params)
    {
        try
        {
            if($params['type']=='delete')
            {
                kernel::single('sysuser_passport')->unSetAccount($params);
            }
            else
            {
                kernel::single('sysuser_passport')->setAccount($params);
            }
        }
        catch(LogicException $e)
        {
            throw new \LogicException($e->getMessage());
        }
        return true;
    }
}
