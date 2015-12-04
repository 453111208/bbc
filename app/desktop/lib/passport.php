<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 * 商家会员,登录注册流程
 */
class desktop_passport {

    public function __construct()
    {
        $this->app = app::get('desktop');
        kernel::single('base_session')->start();
    }

    /**
	* 认证用户名密码以及验证码等
    *
	* @param array $usrdata 认证提示信息
    *
	* @return bool|int返回认证成功与否
	*/
    public function login($data)
    {
        $data = utils::_filter_input($data);

        $accountId = $this->__verifyLogin($data['uname'], $data['password']);

        pamAccount::setSession($accountId, trim($data['uname']));

        return $rows['account_id'];
    }

    private function __verifyLogin($loginName, $password)
    {
        if( empty($loginName) || !$password )
        {
            pamAccount::setLoginErrorCount();
            throw new \LogicException(app::get('desktop')->_('用户名或密码错误'));
        }

        $rows = app::get('desktop')->model('account')->getRow('*',array('login_name'=>trim($loginName),'disabled' => 0) );

        if($rows && pam_encrypt::check($password, $rows['login_password']))
        {
            pamAccount::setLoginErrorCount(true);
        }
        else
        {
            pamAccount::setLoginErrorCount();
            throw new \LogicException(app::get('desktop')->_('用户名或密码错误'));
        }

        return $rows['account_id'];
    }
}

