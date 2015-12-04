<?php
class sysuser_api_user_getUserInfo{
    public $apiDescription = "获取用户的详细信息";

    public function getParams()
    {
        $return['params'] = array(
            'fields' => ['type'=>'field_list','valid'=>'', 'description'=>'查询字段','default'=>'','example'=>''],
        );
        return $return;
    }

    public function getList($params)
    {
        $userId = $params['oauth']['account_id'];
        
        $userData = kernel::single('sysuser_passport')->memInfo($userId);
        return $userData;
    }
}

