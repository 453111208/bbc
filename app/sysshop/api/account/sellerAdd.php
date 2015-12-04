<?php

class sysshop_api_account_sellerAdd {

    public $apiDescription = "创建商家子账号";

    public function getParams()
    {
        $return['params'] = array(
            'login_account' => ['type'=>'string','valid'=>'required','description'=>'子帐号登录用户名','default'=>'','example'=>'1'],
            'login_password' => ['type'=>'string','valid'=>'required','description'=>'子帐号密码','default'=>'','example'=>'1'],
            'psw_confirm' => ['type'=>'string','valid'=>'required','description'=>'子帐号确认密码','default'=>'','example'=>'1'],
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
            'role_id' => ['type'=>'string','valid'=>'required','description'=>'子帐号绑定角色ID','default'=>'','example'=>'1'],
            'name' => ['type'=>'string','valid'=>'required','description'=>'姓名','default'=>'','example'=>'李二'],
            'mobile' => ['type'=>'string','valid'=>'required','description'=>'手机号','default'=>'','example'=>'13918765432'],
            'email' => ['type'=>'string','valid'=>'required','description'=>'邮箱','default'=>'','example'=>'example@shopex.cn'],
        );

        return $return;
    }

    public function save($params)
    {
        $params['seller_type'] = '1';
        if( !$params['role_id'] || $params['role_id'] == '0' )
        {
            throw new \LogicException('请选择角色');
        }

        return shopAuth::signupSeller($params, true);
    }
}

