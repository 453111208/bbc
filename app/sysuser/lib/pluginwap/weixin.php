
<?php
class sysuser_pluginwap_weixin
{
    public $dialog_url = 'https://open.weixin.qq.com/connect/qrconnect';
    public $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    public $user_url = 'https://api.weixin.qq.com/sns/userinfo';

    public function __construct($app)
    {
        $this->my_url = url::action("topm_ctl_trustlogin@callBack",array('actionType'=>'sysuser_pluginwap_weixin'));
        if (preg_match("/^(http):\/\/?([^\/]+)/i", $this->my_url, $matches)){
            $this->my_url = str_replace('http://','',$this->my_url);
            $this->my_url = preg_replace("|/+|","/", $this->my_url);
            $this->my_url = "http://" . $this->my_url;
        } else {
            $this->my_url = str_replace('https://','',$this->my_url);
            $this->my_url = preg_replace("|/+|","/", $this->my_url);
            $this->my_url = "https://" . $this->my_url;
        }
        $this->app = $app;
        $this->obj_session = kernel::single('base_session');
        $this->obj_session->start();

    }

    //获取appkey
    public function get_appkey()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_weixin');
        return $data['appKey'];
    }
    //获取appSecret
    public function get_appSecret()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_weixin');
        return $data['appSecret'];
    }

    //获取图表和链接
    public function get_logo()
    {
        $_SESSION['weixinst'] = md5(uniqid(rand(), TRUE));
        $status = app::get('sysuser')->getConf('sysuser_plugin_weixin');
        $data['status'] = $status['status'];
        $data['typename'] = $this->name;
        $data['image'] = app::get('sysuser')->res_url.'/images/weixin.png';
        $data['url'] = $this->dialog_url.'?appid='.$this->get_appkey()."&response_type=code&scope=snsapi_login&redirect_uri=" . urlencode($this->my_url) . "&state=". $_SESSION['weixinst'];
        return $data;
    }

    public function callback($data)
    {

        if($data['state'] == $_SESSION['weixinst'])
        {
            $token_url = $this->token_url."?grant_type=authorization_code&"
            ."appid=".$this->get_appkey()."&secret=".$this->get_appSecret()."&code=".$data['code'];
            $response = file_get_contents($token_url);
            $result = json_decode($response,true);
            if (isset($result['errcode']))
            {
                $message = "error :" . $result['errcode'] . "msg  :".$result['errmsg'];
                throw new \LogicException($message);
            }
            //通过接口获取用户信息
            $userinfo_url = $this->user_url."?access_token=".$result['access_token']."&openid=".$result['openid'];
            $info  = file_get_contents($userinfo_url);
            $userinfo = json_decode($info,true);
            $userdata = $this->getUserInfo($userinfo);
            $datainfo = array(
                'rsp'=>'succ',
                'data'=>$userdata,
                'type'=>'wap',
            );
            return $datainfo;
        }
        else
        {
            throw new \LogicException(app::get('sysuser')->_('数据错误'));
        }
    }

    public function getUserInfo($userinfo)
    {
        $userdata['openid'] = $userinfo['openid']?$userinfo['openid']:' ';
        $userdata['realname'] = $userinfo['data']['nickname']?$userinfo['data']['nickname']:' ';
        $userdata['nickname'] = $userinfo['data']['nickname']?$userinfo['data']['nickname']:' ';
        $userdata['avatar'] = $userinfo['headimgurl']?$userinfo['headimgurl']:' ';
        $userdata['url'] = $userinfo['headimgurl']?$userinfo['headimgurl']:' ';
        $userdata['gender'] = $userinfo['sex']?$userinfo['sex']:' ';
        $userdata['address'] = $userinfo['province'].'/'.$userinfo['city'];
        $userdata['province'] = $userinfo['province']?$userinfo['province']:' ';
        $userdata['city'] = $userinfo['city']?$userinfo['city']:' ';
        return $userdata;
    }

}