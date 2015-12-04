
<?php
class sysuser_pluginwap_kaixin
{
    public $dialog_url = 'http://api.kaixin001.com/oauth2/authorize';
    public $token_url = 'https://api.kaixin001.com/oauth2/access_token';
    public $user_url = 'https://api.kaixin001.com/users/me.json';
    public function __construct($app)
    {
        $this->my_url = url::action("topm_ctl_trustlogin@callBack",array('actionType'=>'sysuser_pluginwap_kaixin'));
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
        $this->back_url = url::action('topc_ctl_passport@signin');
    }

    //获取appid
    public function get_appkey()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_kaixin');
        return $data['appKey'];
    }
    //获取appkey
    public function get_appSecret()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_kaixin');
        return $data['appSecret'];
    }

    //获取图表和链接
    public function get_logo()
    {
        $_SESSION['kaixinst'] = md5(uniqid(rand(), TRUE));
        $status = app::get('sysuser')->getConf('sysuser_plugin_kaixin');
        $data['status'] = $status['status'];
        $data['typename'] = $this->name;
        $data['image'] = app::get('sysuser')->res_url.'/images/kaixin.png';
        $data['url'] = $this->dialog_url.'?response_type=code&client_id='.$this->get_appkey()."&redirect_uri=".urlencode($this->my_url)."&state=".$_SESSION['kaixinst']."&display=touch";

        return $data;
    }

    public function callback($data)
    {
        if($data['state']==$_SESSION['kaixinst'])
        {
            if($data['error'])
            {
               echo "<script>top.window.location='".$this->back_url."'</script>";
               exit;
            }
            $token_url = $this->token_url."?grant_type=authorization_code&"
            ."client_id=".$this->get_appkey()."&redirect_uri=".urlencode($this->my_url)
            ."&client_secret=".$this->get_appSecret()."&code=".$data['code'];
            $response = file_get_contents($token_url);

            $params = json_decode($response,true);
            if ($params['error'])
            {
                $message = "error :" . $params['error'] . "msg  :".$params['error_description'];
                throw new \LogicException($message);
            }

            $fields = 'uid,name,gender,hometown,city,status,logo120,logo50';
            $userinfo_url = $this->user_url."?access_token=".$params['access_token'].'&fields='.$fields;

            $info  = file_get_contents($userinfo_url);
            $userinfo = json_decode($info,true);

            if($userinfo['uid'])
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
        $userdata['openid'] = $userinfo['uid'];
        $userdata['realname'] = $userinfo['name'];
        $userdata['nickname'] = $userinfo['name'];
        $userdata['avatar'] = $userinfo['logo50'];
        $userdata['url'] = $userinfo['logo120'];
        $userdata['gender'] = $userinfo['gender'];
        $userdata['address'] = $userinfo['hometown'];
        $userdata['province'] = $userinfo['province']?$userinfo['province']:' ';
        $userdata['city'] = $userinfo['city'];
        return $userdata;
    }

}