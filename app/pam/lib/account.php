<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
/**
* 该类是所有登录方式验证后写日志和SESSION的类
*/
class pam_account {

    public  $authType;
	/**
	* 构造方法
	* @param string $type 设置登录体系类型 shopadmin,member等
	*/
    public function __construct($appId)
    {
        kernel::single('base_session')->start();
        if( $appId )
        {
            $this->setAuthType($appId);
            $this->appId = $appId;
        }

    }

    public function setAuthType($appId)
    {
        $this->authType = $this->getAuthType($appId);

        return true;
    }

    /**
     * 设置登录注册后的SESSION
     *
     * @param int $accountId 用户ID
     * @param string $account 用户名
     */
    public function setSession($accountId, $account)
    {
        $_SESSION['account'][$this->authType]['id'] = $accountId;
        $_SESSION['account'][$this->authType]['account'] = $account;
        return true;
    }

    public function logout()
    {
        unset($_SESSION['account'][$this->authType]);
        return true;
    }

    /**
     * 返回登录ID
     */
    public function getAccountId()
    {
        return $_SESSION['account'][$this->authType]['id'];
    }

    /**
     * 返回登录账号
     */
    public function getLoginName()
    {
        return $_SESSION['account'][$this->authType]['account'];
    }

    /**
     * 检查是否登录
     *
     * @return bool
     */
    public function check()
    {
        return $this->getAccountId($this->authType) ? true : false;
    }

    /**
     * 设置登录错误次数
     *
     * @param bool $isrest 是否重置错误次数
     *
     * @return bool
     */
    public function setLoginErrorCount($isreset=false)
    {
        if( $isreset )
        {
            $_SESSION['account'][$this->authType]['error_count'] = 0;
        }
        else
        {
            $_SESSION['account'][$this->authType]['error_count'] += 1;
        }

        return true;
    }

    /**
     * 返回当前登录错误次数
     */
    public function getLoginErrorCount()
    {
        return $_SESSION['account'][$this->authType]['error_count'];
    }

    /**
	* 判断验证码是否开启
    *
	* @return bool 返回验证码开启状态
	*/
    public function isEnableVcode($appId)
    {
        $this->appId = $appId;
        if( !class_exists($this->appId.'_service_vcode') )
        {
            return false;
        }
        else
        {
            $objLibServiceVcode = kernel::single($this->appId.'_service_vcode');
        }

        return $objLibServiceVcode->status();
    }

	/**
	* 安装时，注册体系类型
	* @access public
	* @param string $app_id appid
	* @param string $type 传入的体系的值
	* @param string $name 体系名称
	*/
    public function registerAuthType($appId, $type, $name)
    {
        $accountTypes = app::get('pam')->getConf('account_type');
        $accountTypes[$appId] = array('name' => $name, 'type' => $type);
        app::get('pam')->setConf('account_type',$accountTypes);
        return true;
    }

	/**
	* 注销体系类型
	* @access public
	* @param string $appId appid
	*/
    public function unregisterAuthType($appId)
    {
        $accountTypes = app::get('pam')->getConf('account_type');
        unset($accountTypes[$appId]);
        app::get('pam')->setConf('account_type',$accountTypes);
        return true;
    }

	/**
	* 返回体系类型
	* @access public
	* @param string $app_id appid
	* @return string 返回体系字符串
	*/
    public function getAuthType($appId='desktop')
    {
        $aType = app::get('pam')->getConf('account_type');
        return $aType[$appId]['type'];
    }//End Function
}

