
<?php
class sysuser_pluginwap_baidu
{
    public $dialog_url = 'http://openapi.baidu.com/oauth/2.0/authorize';
    public $token_url = 'https://openapi.baidu.com/oauth/2.0/token';
    public $user_url = 'https://openapi.baidu.com/rest/2.0/passport/users/getInfo';

    public function __construct($app)
    {
        $this->my_url = url::action("topm_ctl_trustlogin@callBack",array('actionType'=>'sysuser_pluginwap_baidu'));
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
        $this->back_url = url::action('topm_ctl_passport@signin');
    }

    //获取appkey
    public function get_appkey()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_baidu');
        return $data['appKey'];
    }
    //获取appSecret
    public function get_appSecret()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_baidu');
        return $data['appSecret'];
    }

    //获取图表和链接
    public function get_logo()
    {
        $_SESSION['baidust'] = md5(uniqid(rand(), TRUE));
        $status = app::get('sysuser')->getConf('sysuser_plugin_baidu');
        $data['status'] = $status['status'];
        $data['typename'] = $this->name;
        $data['image'] = app::get('sysuser')->res_url.'/images/baidu.png';
        $data['url'] = $this->dialog_url.'?client_id='.$this->get_appkey()."&redirect_uri=" . urlencode($this->my_url) . "&state=". $_SESSION['baidust']."&response_type=code&display=mobile";
        return $data;
    }

    public function callback($data)
    {
        if($data['state'] == $_SESSION['baidust'])
        {
            if($data['error'])
            {
               echo "<script>top.window.location='".$this->back_url."'</script>";
               exit;
            }
            $params['grant_type'] = 'authorization_code';
            $params['client_id'] = $this->get_appkey();
            $params['client_secret'] = $this->get_appSecret();
            $params['redirect_uri'] = $this->my_url;
            $params['code'] = $data['code'];
            $res = kernel::single('base_httpclient')->post($this->token_url,$params);
            $result = json_decode($res,true);
            if ($result['error'])
            {
               echo "<h3>error:</h3>" . $result['error'];
               echo "<h3>msg  :</h3>" . $result['error_description'];
               exit;
            }
            //通过接口获取用户信息
            $fmt['access_token'] = $result['access_token'];
            $info = kernel::single('base_httpclient')->post($this->user_url,$fmt);
            $userinfo = json_decode($info,true);
            if($userinfo['userid'])
            {
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
                throw new \LogicException(app::get('sysuser')->_('参数错误！'));
            }
        }
        else
        {
            throw new \LogicException(app::get('sysuser')->_('数据错误'));
        }
    }

    public function getUserInfo($userinfo)
    {
        $userdata['openid'] = $userinfo['userid']?$userinfo['userid']:' ';
        $userdata['realname'] = $userinfo['realname']?$userinfo['realname']:' ';
        $userdata['nickname'] = $userinfo['username']?$userinfo['username']:' ';
        $userdata['avatar'] = $userinfo['portrait']?$userinfo['portrait']:' ';
        $userdata['url'] = $userinfo['portrait']?$userinfo['portrait']:' ';
        $userdata['gender'] = $userinfo['sex']?$userinfo['sex']:' ';
        $userdata['address'] = $userinfo['location']?$userinfo['location']:' ';
        $userdata['province'] = $userinfo['province']?$userinfo['province']:' ';
        $userdata['city'] = $userinfo['city']?$userinfo['city']:' ';
        return $userdata;
    }

}