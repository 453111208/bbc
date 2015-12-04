<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class pam_auth_user
{
    const appId = 'pam';

    protected $userInfo = null;

    protected $accountInfo = null;

    protected $pamAccount = null;

    //登录是否显示验证码登录次数最大值
    protected $loginShowVcodeMaxNumber = 3;


	/**
	 * The user Id we last attempted to retrieve.
	 *
	 * @var \Illuminate\Contracts\Auth\Authenticatable
	 */
    protected $lastAttemptedUserId;

    public function __construct()
    {
        $this->pamAccount = kernel::single('pam_account', 'sysuser');
    }

	/**
	 * 检测用户是否登陆
	 *
	 * @return bool
	 */
    public function check()
    {
        return $this->pamAccount->check();
    }

	/**
	 * 检测用户是否未登陆
	 *
	 * @return bool
	 */
    public function guest()
    {
        return !$this->check();
    }

	/**
	 * 获取用户真实姓名
	 *
	 * @return bool
	 */
    public function getUserName()
    {
        $this->initUserInfo();
        return $this->userInfo['username'];
    }

	/**
	 * 获取用户昵称
	 *
	 * @return bool
	 */
    public function getNickName()
    {
        $this->initUserInfo();
        return $this->userInfo['name'];
    }

	/**
	 * 获取用户登陆名
	 *
	 * @return bool
	 */
    public function getLoginName()
    {
        return $this->pamAccount->getLoginName();
    }

	/**
	 * 获取用户ID
	 *
	 * @return bool
	 */
    public function id()
    {
        return $this->pamAccount->getAccountId();
    }

	/**
	 * 获取用户ID. id()的别名
	 *
	 * @return bool
	 */
    public function getAccountId()
    {
        return $this->id();
    }

	/**
	 * 获取用户ID. id()的别名
	 *
	 * @return bool
	 */
    protected function initUserInfo()
    {
        if (!$this->userInfo) $this->userInfo = app::get(pam_auth_user::appId)->rpcCall('user.get.info', [], 'buyer');
        return true;
    }

	/**
	 * 通过账号名获取账号基本信息
	 *
     * 目前获取 user_id,email,mobile,email_verify
	 *
	 * @param string $loginName
	 * @return bool
	 */
    public function getAccountInfo($loginName)
    {
        return app::get(pam_auth_user::appId)->rpcCall('user.get.account.info', ['user_name' => $loginName], 'buyer');
    }

	/**
	 * 获取用户ID. id()的别名
	 *
	 * @return bool
	 */
    public function getUserInfo()
    {
        if (!$this->userInfo) $this->initUserInfo();
        return $this->userInfo;
    }

	/**
	 * 设置cookie
	 *
	 * @return bool
	 */
    protected function setCookie($name, $value, $expire=false, $path)
    {
        $path = $path ?: kernel::base_url().'/';
        $life = 315360000;
        $expire = $expire === false ? time() + $life : $expire;
        setcookie($name, $value, $expire, $path);
        $_COOKIE[$name] = $value;
        return true;
    }

	/**
	 * 同步用户名cookie
	 *
	 * @return bool
	 */
    protected function syncCookieWithUserName($loginName = '')
    {
        return $this->setCookie('UNAME', $loginName);
    }

	/**
	 * 同步购物车数量cookie
	 *
	 * @param  string  $key
	 * @return bool
	 */
    public function syncCookieWithCartNumber($cartNumber)
    {
        return $this->setCookie('CARTNUMBER', $cartNumber);
    }

	/**
	 * 验证用户
	 *
	 * @param string $logingName
	 * @param string $password
	 * @return bool
	 */
    public function validate($loginName, $password)
    {
        return $this->attempt($loginName, $password, false);
    }

	/**
     * 尝试验证登陆
     *
     * 如果$login参数设置为true, 则验证通过后进行登陆
	 *
	 * @param string $logingName
	 * @param string $password
	 * @param bool $login
	 * @return bool
	 */
    public function attempt($loginName, $password, $login = true)
    {
        $userId = app::get(pam_auth_user::appId)->rpcCall('user.login',
                                                          ['user_name' => $loginName, 'password' => $password]);

        $this->lastAttemptedUserId = $userId;

        if ($login) $this->login($userId, $loginName);

        $this->clearAttemptNumber();
        return true;
    }

    /**
     * 是否显示验证码
     *
     * @param $type 是否显示验证码类型 登录|注册
     *
     * @return bool true 需要显示验证码 false 不需要显示
     */
    public function isShowVcode($type='login')
    {
        $number = $this->getAttemptNumber();
        return $number >= $this->loginShowVcodeMaxNumber ? true : false;
    }

    /**
     * 获取用户登录验证次数
     */
    private function getAttemptNumber()
    {
        $number = $_SESSION['account'][$this->pamAccount->getAuthType()]['error_number'];
        return $number ? $number : 0;
    }

    /**
     * 设置用户登录验证次数
     */
    public function setAttemptNumber()
    {
        $number = $this->getAttemptNumber() + 1;
        if( $number <= $this->loginShowVcodeMaxNumber )
        {
            $_SESSION['account'][$this->pamAccount->getAuthType()]['error_number'] = $number;
        }
        return true;
    }

    public function clearAttemptNumber()
    {
        unset($_SESSION['account'][$this->pamAccount->getAuthType()]['error_number']);
        return true;
    }

    /**
	 * 获取最后验证的用户USER ID
	 *
	 * @return misc
	 */
    public function getLastAttemptedUserId()
    {
        return $this->lastAttemptedUserId;
    }

    /**
	 * 用户登陆进入系统
	 *
	 * @param int
	 * @param string $password
	 * @return bool
	 */
    public function login($userId, $loginName = null)
    {
        if (!$loginName)
        {
            $loginName = current(app::get(pam_auth_user::appId)->rpcCall('user.get.account.name', ['user_id' => $userId], 'buyer'));
        }

        $this->pamAccount->setSession($userId, $loginName);
        $this->syncCookieWithUserName($this->getLoginName());

        //登录同步购物车数据
        $cartNumber = app::get(pam_auth_user::appId)->rpcCall('trade.cart.getCount', ['user_id' => $userId]);
        $this->syncCookieWithCartNumber($cartNumber);
        return true;
    }

	/**
	 * 设置登录名
	 *
	 * @param  string  $userName
	 * @return bool
	 */
    public function updateLoginName($loginName)
    {
        $params = array(
            'user_id' => $this->id(),
            'user_name' => $loginName,
        );

        if (app::get('pam')->rpcCall('user.account.update', $params, 'buyer') ? true : false)
        {
            $this->syncCookieWithUserName($loginName);
        }
    }

    /**
	 * 用户登出系统
	 *
	 * @param int
	 * @param string $password
	 * @return bool
	 */
    public function logout()
    {
        $this->pamAccount->logout();
        $this->syncCookieWithCartNumber(0);
        $this->syncCookieWithUserName();
    }

    public function signUp($loginName, $password, $confirmedPassword)
    {
        return app::get('pam')->rpcCall('user.create',
                                         ['account' => $loginName,
                                          'password' => $password,
                                          'pwd_confirm' => $confirmedPassword,
                                          'reg_ip' => request::getClientIp()
                                         ],
                                         'buyer');
    }
}
