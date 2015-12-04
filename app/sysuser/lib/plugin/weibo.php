<?php
use GuzzleHttp\Exception\ClientException;

class sysuser_plugin_weibo extends sysuser_plugin_abstract implements sysuser_interface_trust
{

	/**
	 * 信任登陆标题名
	 *
	 * @var string
	 */
    protected $name = '新浪微博';

	/**
	 * 唯一标示名
	 *
	 * @var string
	 */
    protected $flag = 'weibo';

	/**
	 * 版本号
	 *
	 * @var float
	 */
    protected $version = '2.0';

	/**
	 * 信任登陆相关url地址
	 *
	 * @var array
	 */
    protected $authUrls = ['web' => ['authorize' => 'https://api.weibo.com/oauth2/authorize',
                                  'token' => 'https://api.weibo.com/oauth2/access_token',
                                  'openid' => 'https://api.weibo.com/oauth2/get_token_info',
                                  'userinfo' => 'https://api.weibo.com/2/users/show.json']];


    /**
	 * 获取plugin autherize url
	 *
	 * @param string $state
	 * @return string
	 */
    public function getAuthorizeUrl($state)
    {
        return $this->getUrl('authorize').'?'.http_build_query(['response_type' => 'code',
                                                                'client_id' => $this->getAppKey(),
                                                                'redirect_uri' => $this->getCallbackUrl(),
                                                                'display' => $this->getView() == 'web' ? 'default' : 'wap',
                                                                'state' => $state]);
    }

    /**
	 * 通过调用信任登陆accesstoken接口生成access token
	 *
	 * @param string $code
     * @return string
	 */
    public function generateAccessToken($code)
    {
        $args = ['client_id' => $this->getAppKey(),
                 'client_secret'=> $this->getAppSecret(),
                 'grant_type' => 'authorization_code',
                 'code' => $code,
                 'redirect_uri' => $this->getCallbackUrl()];

        try
        {
            $msg = client::post($this->getUrl('token'), ['body' => $args])->json();
        }
        //ClientException 
        catch (ClientException $e)
        {
            $msg = $e->getResponse()->json();
            throw new \LogicException("error :" . $msg['error_code']. "msg  :". $msg['error']);
        }
        
        
        return $msg['access_token'];
    }

    /**
	 * 生成信任登陆open id
	 *
	 * @return string
	 */
    public function generateOpenId()
    {
        $args = ['access_token' => $this->getAccessToken()];
        $msg = client::post($this->getUrl('openid'), ['body' => $args])->json();
        if (isset($msg['error'])) throw new \LogicException("error :" . $msg['error_code']. "msg  :". $msg['error']);

        return $msg['uid'];
    }

    /**
	 * 通过调用信任登陆相关用户接口生成用户info
	 *
	 * @param string $code
	 * @return string
	 */
    public function generateUserInfo()
    {
        $args = ['access_token' => $this->getAccessToken(),
                 'uid' => $this->getOpenId()];

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
        return $userInfo = ['openid' => $this->getOpenId(),
                            'access_token' => $this->getAccessToken(),
                            'nickname' => $trustUserInfo['screen_name'],
                            'figureurl' => $trustUserInfo['profile_image_url']];
    }
}