<?php
use GuzzleHttp\Exception\ClientException;

class sysuser_plugin_renren extends sysuser_plugin_abstract implements sysuser_interface_trust
{
    public $name = '人人网';
    public $flag = 'renren';
    public $version = '2.0';

    public $authUrls = ['web' => ['authorize' => 'https://graph.renren.com/oauth/authorize',
                                  'token' => 'https://graph.renren.com/oauth/token'],
                        'wap' => ['authorize' => 'https://graph.renren.com/oauth/authorize',
                                  'token' => 'https://graph.renren.com/oauth/token']];

    protected $tmpOpenId = null;
    protected $tmpUserInfo = [];
    
    public function getAuthorizeUrl($state)
    {
        return $this->getUrl('authorize').'?'.http_build_query(['response_type' => 'code',
                                                                    'client_id' => $this->getAppKey(),
                                                                    'redirect_uri' => $this->getCallbackUrl(),
                                                                    'state' => $state,
                                                                    'display' => ($this->getView() == 'web' ? 'page' : 'touch')]);
    }

    public function generateAccessToken($code)
    {
        $args = ['client_id' => $this->getAppKey(),
                 'client_secret'=> $this->getAppSecret(),
                 'grant_type' => 'authorization_code',
                 'code' => $code,
                 'token_type' => 'bearer',
                 'redirect_uri' => $this->getCallbackUrl()];

        try
        {
            $msg = client::post($this->getUrl('token'), ['body' => $args])->json();
        }
        catch (ClientException $e)
        {
            $msg = $e->getResponse()->json();
            throw new \LogicException("error :" . $msg['invalid_grant_code']. "msg  :". $msg['error_description']. "code :".$msg['error_code']);
        }
        


        $this->tmpOpenId = $msg['user']['id'];
        $this->tmpUserInfo = $msg['user'];

        return $msg['access_token'];
    }
    

    public function generateOpenId()
    {
        return $this->tmpOpenId;
    }

    
    public function generateUserInfo()
    {
        return $this->convertStandardUserInfo($this->tmpUserInfo);
    }

    public function convertStandardUserInfo($trustUserInfo)
    {
        return $userInfo = ['openid' => $this->getOpenId(),
                            'access_token' => $this->getAccessToken(),
                            'nickname' => $trustUserInfo['name'],
                            'figureurl' => $trustUserInfo['avatar'][0]['url']];
        
    }

}