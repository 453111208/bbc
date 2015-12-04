<?php
class sysuser_plugin_abstract
{
	/**
	 * 信任登陆标题名
	 *
	 * @var string
	 */
    protected $name;

	/**
	 * 唯一标示名
	 *
	 * @var string
	 */
    protected $flag;

	/**
	 * 版本号
	 *
	 * @var float
	 */
    protected $version;

	/**
	 * 信任登陆相关url地址
	 *
	 * @var array
	 */
    protected $authUrls;

	/**
	 * callback url地址.
	 *
	 * @var string
	 */
    protected $callbackUrl;

	/**
	 * 访问源, 手机端或PC端
	 *
	 * @var string
	 */
    protected $view;

	/**
	 * 支持视图
	 *
	 * @var array
	 */
    protected $supportView = ['web', 'wap'];

	/**
	 * 默认视图
	 *
	 * @var array
	 */
    protected $defaultView = 'web';

	/**
	 * 配置
	 *
	 * @var string
	 */
    protected $setting;

	/**
	 * acess token
	 *
	 * @var string
	 */
    protected $accessToken;

	/**
	 * open id/信任登陆用户唯一标识
	 *
	 * @var string
	 */
    protected $openId;

	/**
	 * 设置callback url
	 *
	 * @param string $callbackUrl
	 * @return self
	 */    
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

	/**
	 * 获取callback url
	 *
	 * @param string $callbackUrl
	 * @return self
	 */    
    public function getCallbackUrl()
    {
        if (!$this->callbackUrl) throw new \ErrorException('Must be set callback url.');
        return $this->callbackUrl;
    }

	/**
	 * 获取当前plugin的唯一标识flag
	 *
	 * @param string $callbackUrl
	 * @return string
	 */    
    public function getFlag()
    {
        return $this->flag;
    }


	/**
	 * 获取用户基本信息
	 *
	 * @return string
	 */    
    public function getInfo()
    {
        $info = ['name' => $this->name,
                 'flag' => $this->flag,
                 'version' => $this->version,
                 'status' => $this->getStatus()];
        return $info;
    }

	/**
	 * 获取前端用户基本信息
	 *
	 * @return string
	 */    
    public function getFrontInfo($state)
    {
        $info = ['name' => $this->name,
                 'flag' => $this->flag,
                 'image' => $this->getLogoUrl(),
                 'url' => $this->getAuthorizeUrl($state)];
        return $info;
    }

	/**
	 * 获取当前信任登陆logo url地址
	 *
	 * @return string
	 */    
    public function getLogoUrl()
    {
        return app::get('sysuser')->res_url.'/images/login/'.$this->flag.'.png';
    }

	/**
	 * 设置当前视图
	 *
	 * @return string
	 */    
    public function setView($view)
    {
        if (!in_array($view, $this->supportView)) throw new \ErrorException('Can not support view:'.$view);
        $this->view = $view;
        return $this;
    }

	/**
	 * 获取当前视图
	 *
	 * @return string
	 */    
    public function getView()
    {
        if (!$this->view) throw new \ErrorException('Must be set view.');
        return $this->view;
    }

    
	/**
	 * 设置配置
	 *
	 * @return array
	 */    
    public function setSetting($setting)
    {
        return app::get('sysuser')->setConf(get_class($this), $setting);
    }

	/**
	 * 获取配置
	 *
	 * @param string $key
	 * @return misc
	 */    
    public function getSetting($key = null)
    {
        if (!$this->setting)
        {
            $this->setting = app::get('sysuser')->getConf(get_class($this));
        }
        return $key ? $this->setting[$key] : $this->setting;
    }

	/**
	 * 获取app key
	 *
	 * @param string $key
	 * @return misc
	 */    
    public function getAppKey()
    {
        return $this->getSetting('appKey');
    }

	/**
	 * 获取app secret
	 *
	 * @param string $key
	 * @return misc
	 */    
    public function getAppSecret()
    {
        return $this->getSetting('appSecret');
    }

	/**
	 * 获取当前plugin状态 
	 *
	 * @param string $key
	 * @return misc
	 */ 
    public function getStatus()
    {
        return $this->getSetting('status') ? 1 : 0;
    }

	/**
	 * 根据视图类型获取量$authUrls对应视图地址
	 *
	 * @param string $api
	 * @return string
	 */    
    public function getUrl($api)
    {
        return $this->authUrls[$this->view][$api] ?: ($this->authUrls[$this->defaultView][$api] ?: false);
    }

    /**
	 * 根据user flag和plugin flag生成luckymall用户唯一标识
	 *
	 * @param string $userFlag
	 * @return string
	 */
    protected function generateUserFlag($userFlag)
    {
        return md5($userFlag).'@'.$this->flag.'.cn';
    }

    /**
	 * 获取luckymall用户唯一标识n
	 *
	 * @return string
	 */
    public function getUserFlag()
    {
        return $this->generateUserFlag($this->getOpenId());
    }

    public function getAccessToken($code = null)
    {
        if (!$this->accessToken)
        {
            if (!$code) throw new \LogicException('must be have code');
            $this->accessToken = $this->generateAccessToken($code);
        }
        return $this->accessToken;
    }

    public function getOpenId()
    {
        if (!$this->openId)
        {
            $this->openId = $this->generateOpenId();
        }
        return $this->openId;
    }

    public function getUserInfo()
    {
        if (!$this->userInfo)
        {
            $this->userInfo = $this->generateUserInfo();
        }
        return $this->userInfo;
    }
    /**
	 * 信任登陆callback认证
	 *
	 * @param string $api
	 * @return string
	 */
    public function authorize($state, $params)
    {
        $returnState = $params['state'];
        if ($state == $returnState)
        {
            $code = $params['code'];
            $accessToken = $this->getAccessToken($code);
        }
        return $this->getUserFlag();
    }
}