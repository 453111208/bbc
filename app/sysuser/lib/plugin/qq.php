<?php
class sysuser_plugin_qq extends sysuser_plugin_abstract implements sysuser_interface_trust
{
    public $name = 'QQ登陆';
    public $flag = 'qq';
    public $version = '2.0';
    public $authUrls = ['web' => ['authorize' => 'https://graph.qq.com/oauth2.0/authorize',
                                  'token' => 'https://graph.qq.com/oauth2.0/token',
                                  'openid' => 'https://graph.qq.com/oauth2.0/me',
                                  'userinfo' => 'https://graph.qq.com/user/get_user_info']];
                        /*
                        'wap' => ['authorize' => 'https://graph.z.qq.com/moc2/authorize',
                                  'token' => 'https://graph.z.qq.com/moc2/token',
                                  'openid' => 'https://graph.z.qq.com/moc2/me']];
                        */

    public function getAuthorizeUrl($state)
    {
        $args = ['response_type' => 'code',
                 'client_id' => $this->getAppKey(),
                 'redirect_uri' => $this->getCallbackUrl(),
                 'state' => $state];
        if ($this->getView() == 'wap') $args['display'] = 'mobile';

        return $this->getUrl('authorize').'?'.http_build_query($args);
    }

    public function generateAccessToken($code)
    {
        $args = ['client_id' => $this->getAppKey(),
                 'client_secret'=> $this->getAppSecret(),
                 'grant_type' => 'authorization_code',
                 'code' => $code,
                 'redirect_uri' => $this->getCallbackUrl()];

        $response = client::get($this->getUrl('token'), ['query' => $args])->getBody();
        switch ($this->getView())
        {
            case 'web':
                if (preg_match('/^callback\(.+\)/i', $response, $matches))
                {
                    $response = $matches[1];
                }
                $msg = json_decode($response, true);
                if ($msg['error']) throw new \LogicException("error :" . $msg->error . "msg  :". $msg->error_description);
                parse_str($response, $msg);
                break;
           case 'wap':
               parse_str($response, $msg);
               if ($msg['code']) throw new \LogicException("error :" . $msg['code'] . "msg  :". $msg->error_description);
               break;
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
        $response = client::get($this->getUrl('openid'), ['query' => $args])->getBody();

        switch ($this->getView())
        {
            case 'web':
                if (!preg_match('/^callback\((.+)\)/i', $response, $matches)) throw new \LogicException('数据错误');
                $msg = json_decode($matches[1], true);
                
                if ($msg['error']) throw new \LogicException("error :" . $msg->error . "msg  :". $msg->error_description);
                break;
           case 'wap':
               parse_str($response, $msg);
               if ($msg['code']) throw new \LogicException("error :" . $msg['code'] . "msg  :". $msg->error_description);
               break;
        }
        return $msg['openid'];
    }

    public function generateUserInfo()
    {
        $args = ['access_token' => $this->getAccessToken(),
                 'oauth_consumer_key' => $this->getAppKey(),
                 'openid' => $this->getOpenId()];
        $msg = client::get($this->getUrl('userinfo'), ['query' => $args])->json();


        if($msg['ret']!==0) throw new \LogicException(app::get('sysuser')->_('参数错误！'));

        return $this->convertStandardUserInfo($msg);
    }

    protected function convertStandardUserInfo($trustUserInfo)
    {
        return $userInfo = ['openid' => $this->getOpenId(),
                            'access_token' => $this->getAccessToken(),
                            'nickname' => $trustUserInfo['nickname'],
                            'figureurl' => $trustUserInfo['figureurl']];
    }
}