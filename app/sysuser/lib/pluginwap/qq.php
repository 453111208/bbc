
<?php
class sysuser_pluginwap_qq
{
    public $dialog_url = 'https://graph.qq.com/oauth2.0/authorize';
    public $token_url = 'https://graph.qq.com/oauth2.0/token';
    public $me_url = 'https://graph.qq.com/oauth2.0/me';
    public $user_url = 'https://graph.qq.com/user/get_user_info';
    public function __construct()
    {
        kernel::single('base_session')->start();
        $this->my_url = url::action("topm_ctl_trustlogin@callBack",array('actionType'=>'sysuser_pluginwap_qq'));
        if (preg_match("/^(http):\/\/?([^\/]+)/i", $this->my_url, $matches)){
            $this->my_url = str_replace('http://','',$this->my_url);
            $this->my_url = preg_replace("|/+|","/", $this->my_url);
            $this->my_url = "http://" . $this->my_url;
        } else {
            $this->my_url = str_replace('https://','',$this->my_url);
            $this->my_url = preg_replace("|/+|","/", $this->my_url);
            $this->my_url = "https://" . $this->my_url;
        }
    }

    //获取appid
    public function get_appkey()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_qq');
        return $data['appid'];
    }
    //获取appkey
    public function get_appSecret()
    {
        $data = app::get('sysuser')->getConf('sysuser_plugin_qq');
        return $data['appkey'];
    }

    //获取图表和链接
    public function get_logo()
    {
        $_SESSION['qqst'] = md5(uniqid(rand(), TRUE));
        $status = app::get('sysuser')->getConf('sysuser_plugin_qq');
        $data['status'] = $status['status'];
        $data['typename'] = $this->name;
        $data['image'] = app::get('sysuser')->res_url.'/images/qqlogin.png';
        $data['url'] = $this->dialog_url.'?response_type=code&client_id='.$this->get_appkey()."&redirect_uri=" . urlencode($this->my_url) . "&state="
        . $_SESSION['qqst'];
        return $data;
    }

    public function callback($data)
    {
        if($data['state']==$_SESSION['qqst'])
        {
            $token_url = $this->token_url."?grant_type=authorization_code&"
            . "client_id=" . $this->get_appkey() . "&redirect_uri=" . urlencode($this->my_url)
            . "&client_secret=" . $this->get_appSecret() . "&code=" . $data['code'];
            $response = file_get_contents($token_url);

            if (strpos($response, "callback") !== false)
            {
                $lpos = strpos($response, "(");
                $rpos = strrpos($response, ")");
                $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
                $msg = json_decode($response,true);
                if (isset($msg['error']))
                {
                    $message = "error :" . $msg['error'] . "msg  :".$msg['error_description'];
                    throw new \LogicException($message);
                }
            }
            //Step3：使用Access Token来获取用户的OpenID
            $params = array();
            parse_str($response, $params);
            $graph_url = $this->me_url."?access_token=" . $params['access_token'];
            $str  = file_get_contents($graph_url);
            if (strpos($str, "callback") !== false)
            {
                $lpos = strpos($str, "(");
                $rpos = strrpos($str, ")");
                $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
            }
            $user = json_decode($str,true);

            if (isset($user['error']))
            {
                $message = "error :" . $user['error'] . "msg  :".$user['error_description'];
                throw new \LogicException($message);
            }

            $userinfo_url = $this->user_url."?access_token=" . $params['access_token']."&oauth_consumer_key="
            . $this->get_appkey() . "&openid=" . $user['openid'];
            $info  = file_get_contents($userinfo_url);
            $userinfo = json_decode($info,true);

            $userinfo['openid'] = $user['openid'];
            if($userinfo['ret']==0)
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
        $userdata['openid'] = $userinfo['openid'];
        $userdata['realname'] = $userinfo['realname'];
        $userdata['nickname'] = $userinfo['nickname'];
        $userdata['avatar'] = $userinfo['figureurl_2'];
        $userdata['url'] = $userinfo['figureurl'];
        //$userdata['birthday'] = $userinfo['year'];
        $userdata['gender'] = $userinfo['gender'];
        $userdata['address'] = $userinfo['province'].'/'.$userinfo['city'];
        $userdata['province'] = $userinfo['province'];
        $userdata['city'] = $userinfo['city'];
        return $userdata;
    }

}