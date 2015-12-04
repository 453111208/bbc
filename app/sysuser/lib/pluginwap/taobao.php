
<?php
class sysuser_pluginwap_taobao
{
    public $dialog_url = ' https://oauth.taobao.com/authorize';
    public $token_url = 'https://oauth.taobao.com/token';

    public function __construct($app)
    {
        $this->my_url = url::action("topm_ctl_trustlogin@callBack",array('actionType'=>'sysuser_pluginwap_taobao'));
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
        $data = app::get('sysuser')->getConf('sysuser_plugin_taobao');
        return $data['appKey'];
    }
    //获取appSecret
    public function get_appSecret()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_taobao');
        return $data['appSecret'];
    }

    //获取图表和链接
    public function get_logo()
    {
        $_SESSION['taobaost'] = md5(uniqid(rand(), TRUE));
        $status = app::get('sysuser')->getConf('sysuser_plugin_taobao');
        $data['status'] = $status['status'];
        $data['typename'] = $this->name;
        $data['image'] = app::get('sysuser')->res_url.'/images/taobao.png';
        $data['url'] = $this->dialog_url.'?client_id='.$this->get_appkey()."&redirect_uri=" . urlencode($this->my_url)."&state=".$_SESSION['taobaost']."&response_type=code&view=wap";
        return $data;
    }

    public function callback($data)
    {
        if($data['state'] == $_SESSION['taobaost'])
        {
            if ($data['error'])
            {
               echo "<script>top.window.location='".$this->back_url."'</script>";
               exit;
            }
            $params['grant_type'] = 'authorization_code';
            $params['client_id'] = $this->get_appkey();
            $params['client_secret'] = $this->get_appSecret();
            $params['redirect_uri'] = $this->my_url;
            $params['code'] = $data['code'];
            $params['state'] = $_SESSION['taobaost'];
            $res = kernel::single('base_httpclient')->post($this->token_url,$params);
            $result = json_decode($res,true);
            $result['taobao_user_nick'] = urldecode($result['taobao_user_nick']);
            if ($data['error'])
            {
                $message = "error :" . $data['error'] . "msg  :".$data['error_description'];
                throw new \LogicException($message);
            }
            if($result['taobao_user_id'])
            {
                $userdata = $this->getUserInfo($result);
                $datainfo = array(
                    'rsp'=>'succ',
                    'data'=>$userdata,
                    'type'=>'wap',
                );
                return $datainfo;
            }
            else
            {
                throw new \LogicException(app::get('sysuser')->_('参数错误！'));;
            }
           
        }
        else
        {
            throw new \LogicException(app::get('sysuser')->_('数据错误'));
        }
    }
    
    public function getUserInfo($userinfo)
    {
        $userdata['openid'] = $userinfo['taobao_user_id'];
        $userdata['realname'] = $userinfo['taobao_user_nick'];
        $userdata['nickname'] = $userinfo['taobao_user_nick'];
        $userdata['avatar'] = $userinfo['avatar']?$userinfo['avatar']:' ';
        $userdata['url'] = $userinfo['profile_image_url']?$userinfo['profile_image_url']:' ';
        //$userdata['birthday'] = $userinfo['year'];
        $userdata['gender'] = $userinfo['gender']?$userinfo['gender']:' ';
        $userdata['address'] = $userinfo['location']?$userinfo['location']:' ';
        $userdata['province'] = $userinfo['province']?$userinfo['province']:' ';
        $userdata['city'] = $userinfo['city']?$userinfo['city']:' ';
        return $userdata;
    }

}