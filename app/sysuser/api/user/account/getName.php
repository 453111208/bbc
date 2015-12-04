<?php
class sysuser_api_user_account_getName{
    public $apiDescription = "根据会员ID获取对应的用户名";
    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'string','valid'=>'required', 'description'=>'用户ID必填','default'=>'','example'=>''],
        );
        return $return;
    }
    public function getName($params)
    {
        if(!$params['user_id'])
        {
            throw new \LogicException('参数user_id不能为空！');
        }
        $userId = explode(',',$params['user_id']);

        $userNameData = kernel::single('sysuser_passport')->getLoginName($userId);
        return $userNameData;
    }
}
