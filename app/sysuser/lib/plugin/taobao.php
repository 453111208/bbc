<?php

use GuzzleHttp\Exception\ClientException;

class sysuser_plugin_taobao extends sysuser_plugin_abstract implements sysuser_interface_trust
{
    public $name = '淘宝网';
    public $flag = 'taobao';
    public $version = '2.0';
    protected $authUrls = ['web' => ['authorize' => 'https://oauth.taobao.com/authorize',
                                     'token' => 'https://oauth.taobao.com/token'],

                           'wap' => ['authorize' => 'https://oauth.taobao.com/authorize',
                                     'token' => 'https://oauth.taobao.com/token']];

    protected $taobaoUserInfo = null;

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
                                                                $this->getView() == 'web' ? 'web' : 'wap',
                                                                'state' => $state]);
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
        try
        {
            $msg = client::post($this->getUrl('token'), ['body' => $args])->json();
        }
        catch (ClientException $e)
        {
            $msg = $e->getResponse()->json();
            throw new \LogicException("error :" . $msg['error']. "msg  :". $msg['error_description']);
        }
        $this->taobaoUserInfo = ['taobao_user_id' => $msg['taobao_user_id'],
                                 'taobao_user_nick' => $msg['taobao_user_nick']];
        return $msg['access_token'];
    }

    /**
	 * 生成信任登陆open id
	 *
	 * @return string
	 */
    public function generateOpenId()
    {
        return $this->taobaoUserInfo['taobao_user_id'];
    }

    /**
	 * 通过调用信任登陆相关用户接口生成用户info
	 *
	 * @param string $code
	 * @return string
	 */
    public function generateUserInfo()
    {
        return $this->convertStandardUserInfo($this->taobaoUserInfo);
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
                            'nickname' => $trustUserInfo['taobao_user_nick'],
                            'figureurl' => ''];
    }
}