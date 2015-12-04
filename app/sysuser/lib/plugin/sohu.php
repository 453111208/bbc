
<?php
class sysuser_plugin_sohu implements sysuser_interface_trust
{
    public $name = '搜狐网';
    public $app_name = 'sysuser';
    public $ver = '2.0';
    public $view = 'sysuser/trust/sohu.html';

    public $dialog_url = 'https://api.sohu.com/oauth2/authorize';
    public $token_url = 'https://api.sohu.com/oauth2/token';
    public $user_url = 'https://api.sohu.com/rest/pp/prv/1/user/get_info';

    public function __construct()
    {
        kernel::single('base_session')->start();
        $this->my_url = url::action("topc_ctl_trustlogin@callBack",array('actionType'=>'sysuser_plugin_sohu'));
        if (preg_match("/^(http):\/\/?([^\/]+)/i", $this->my_url, $matches)){
            $this->my_url = str_replace('http://','',$this->my_url);
            $this->my_url = preg_replace("|/+|","/", $this->my_url);
            $this->my_url = "http://" . $this->my_url;
        } else {
            $this->my_url = str_replace('https://','',$this->my_url);
            $this->my_url = preg_replace("|/+|","/", $this->my_url);
            $this->my_url = "https://" . $this->my_url;
        }
        $this->back_url = url::action('topc_ctl_passport@signin');
    }

    public function set_setting($data)
    {
       return app::get('sysuser')->setConf('sysuser_plugin_sohu', $data['data']);
    }

    public function get_setting()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_sohu');
        return $data;
    }

    //获取appkey
    public function get_appkey()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_sohu');
        return $data['appKey'];
    }
    //获取appSecret
    public function get_appSecret()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_sohu');
        return $data['appSecret'];
    }

    //获取图表和链接
    public function get_logo()
    {
        $_SESSION['sohust'] = md5(uniqid(rand(), TRUE));
        $status = app::get('sysuser')->getConf('sysuser_plugin_sohu');
        $data['status'] = $status['status'];
        $data['typename'] = $this->name;
        $data['image'] = app::get('sysuser')->res_url.'/images/sohu.png';
        $data['url'] = $this->dialog_url.'?client_id='.$this->get_appkey()."&redirect_uri=" . urlencode($this->my_url) . "&state=". $_SESSION['sohust']."&response_type=code";
        return $data;
    }

    public function callback($data)
    {
        if($data['state'] == $_SESSION['sohust'])
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
            if($result['error'])
            {
                $message = "error :" . $result['error'] . "msg  :".$result['error_description'];
                throw new \LogicException($message);
            }
            //通过接口获取用户信息
            $userinfo_url = $this->user_url."?access_token=".$result['access_token'];
            $info  = file_get_contents($userinfo_url);
            $userinfo = json_decode($info,true);
            if($userinfo['message'] == 'ok')
            {
                $userdata['openid'] = $result['open_id'];
                $userdata = $this->getUserInfo($userinfo);
                $datainfo = array(
                    'rsp'=>'succ',
                    'data'=>$userdata,
                    'type'=>'pc',
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
        $userdata['openid'] = $userinfo['openid']?$userinfo['openid']:' ';
        $userdata['realname'] = $userinfo['data']['uniqname']?$userinfo['data']['uniqname']:' ';
        $userdata['nickname'] = $userinfo['data']['uniqname']?$userinfo['data']['uniqname']:' ';
        $userdata['avatar'] = $userinfo['portrait']?$userinfo['portrait']:' ';
        $userdata['url'] = $userinfo['portrait']?$userinfo['portrait']:' ';
        $userdata['gender'] = $userinfo['sex']?$userinfo['sex']:' ';
        $userdata['address'] = $userinfo['location']?$userinfo['location']:' ';
        $userdata['province'] = $userinfo['province']?$userinfo['province']:' ';
        $userdata['city'] = $userinfo['city']?$userinfo['city']:' ';
        return $userdata;
    }

}