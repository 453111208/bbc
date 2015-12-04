<?php
class sysuser_plugin_kaixin extends sysuser_plugin_abstract implements sysuser_interface_trust
{
    public $name = '开心网';
    public $flag = 'kaixin';
    public $version = '2.0';
    public $app_name = 'sysuser';

	/**
	 * 信任登陆相关url地址
	 *
	 * @var array
	 */
    protected $authUrls = ['web' => ['authorize' => 'http://api.kaixin001.com/oauth2/authorize',
                                     'token' => 'https://api.kaixin001.com/oauth2/access_token',
                                     'userinfo' => 'https://api.kaixin001.com/users/me.json'],
                           'wap' => ['authorize' => 'http://api.kaixin001.com/oauth2/authorize',
                                     'token' => 'https://api.kaixin001.com/oauth2/access_token',
                                     'userinfo' => 'https://api.kaixin001.com/users/me.json']];
    
    // public $user_url = 'https://api.kaixin001.com/users/me.json';

    public function getAuthorizeUrl($state)
    {
        return $this->getUrl('authorize').'?'.http_build_query(['client_id' => $this->getAppKey(),
                                                                'response_type' => 'code',
                                                                'state' => $state,
                                                                'redirect_uri' => $this->getCallbackUrl()]);
    }

    /**
	 * 通过调用信任登陆accesstoken接口生成access token
	 *
	 * @param string $code	 * @return string
	 */
    public function generateAccessToken($code)
    {
        $args = ['client_id' => $this->getAppKey(),
                 'client_secret'=> $this->getAppSecret(),
                 'grant_type' => 'authorization_code',
                 'code' => $code,
                 'redirect_uri' => $this->getCallbackUrl()];

        $msg = client::post($this->getUrl('token'), ['body' => $args])->json();
        if (isset($msg['error'])) throw new \LogicException("error :" . $msg['error_code']. "msg  :". $msg['error']);

        return $msg['access_token'];
    }

    /**
	 * 生成信任登陆open id
	 *
	 * @return string
	 */
    public function generateOpenId()
    {
        $userInfo = $this->getUserInfo();
        return $userInfo['openid'];
    }

    /**
	 * 通过调用信任登陆相关用户接口生成用户info
	 *
	 * @param string $code
	 * @return string
	 */
    public function generateUserInfo()
    {
        $args = ['access_token' => $this->getAccessToken()];

        $msg = client::get($this->getUrl('userinfo'), ['query' => $args])->json();

        if ($msg['error']) throw new \LogicException("error :" . $msg['error_code']. "msg  :". $msg['error']);

        return $this->convertStandardUserInfo($msg);
    }

    /**
	 * 转换信任登陆接口获取的用户信息为luckymall标准用户信息 
	 *
	 * @param string $code
	 * @return string
	 */
    protected function convertStandardUserInfo($trustUserInfo)
    {
        return $userInfo = ['openid' => $trustUserInfo['uid'],
                            'access_token' => $this->getAccessToken(),
                            'nickname' => $trustUserInfo['name'],
                            'figureurl' => $trustUserInfo['logo50']];
    }    
}