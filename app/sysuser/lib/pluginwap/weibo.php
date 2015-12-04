
<?php
class sysuser_pluginwap_weibo
{
    public $dialog_url = 'https://api.weibo.com/oauth2/authorize';
    public $token_url = 'https://api.weibo.com/oauth2/access_token';
    public $user_url = 'https://api.weibo.com/2/users/show.json';

    public function __construct($app)
    {
        $this->my_url = url::action("topm_ctl_trustlogin@callBack",array('actionType'=>'sysuser_pluginwap_weibo'));
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
        $data = app::get('sysuser')->getConf('sysuser_plugin_weibo');
        return $data['appKey'];
    }
    //获取appSecret
    public function get_appSecret()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_weibo');
        return $data['appSecret'];
    }

    //获取图表和链接
    public function get_logo()
    {
        $_SESSION['weibost'] = md5(uniqid(rand(), TRUE));
        $status = app::get('sysuser')->getConf('sysuser_plugin_weibo');
        $data['status'] = $status['status'];
        $data['typename'] = $this->name;
        $data['image'] = app::get('sysuser')->res_url.'/images/weibo.png';
        $data['url'] = $this->dialog_url.'?client_id='.$this->get_appkey()."&redirect_uri=" . urlencode($this->my_url) . "&state="
        . $_SESSION['weibost'];
        return $data;
    }

    public function callback($data)
    {
        if($data['state'] == $_SESSION['weibost'])
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

            $userinfo_url = $this->user_url."?access_token=".$result['access_token']."&uid=".$result['uid'];
            $info  = file_get_contents($userinfo_url);
            $userinfo = json_decode($info,true);

            if($userinfo['id'])
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
        $userdata['openid'] = $userinfo['id'];
        $userdata['realname'] = $userinfo['name'];
        $userdata['nickname'] = $userinfo['screen_name'];
        $userdata['avatar'] = $userinfo['avatar_hd'];
        $userdata['url'] = $userinfo['profile_image_url'];
        //$userdata['birthday'] = $userinfo['year'];
        $userdata['gender'] = $userinfo['gender'];
        $userdata['address'] = $userinfo['location'];
        $userdata['province'] = $userinfo['province'];
        $userdata['city'] = $userinfo['city'];
        return $userdata;
    }

}