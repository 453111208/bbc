<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_ctl_passport extends desktop_controller{

    var $login_times_error=3;


    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    public function index()
    {
        /** 登录之前的预先验证 **/
        if(!defined("STRESS_TESTING"))
        {
            $obj_services = kernel::servicelist('app_pre_auth_use');
            foreach ($obj_services as $obj)
            {
                if (method_exists($obj, 'pre_auth_uses') && method_exists($obj, 'login_verify'))
                {
                    $pagedata['desktop_login_verify'] = $obj->login_verify();
                }
            }
        }
        /** end **/

        //检查证书是否合法,从而判定产品功能是否可用。比如b2c功能
        $certCheckObj = kernel::service("product_soft_certcheck");
        if(is_object($certCheckObj) && method_exists($certCheckObj,"check"))
        {
            $certCheckObj->check();
        }

        $pagedata['desktop'] = url::route('shopadmin');
        $pagedata['redirect'] = input::get('url');

        $pagedata['Commerce']= 'yes';
        $pagedata['img_url']= app::get('desktop')->res_url.'/images/login.png';

        if( pamAccount::isEnableVcode('desktop') )
        {
            $pagedata['show_varycode'] = 'true';
        }

        $pagedata['error_info'] = urldecode(input::get('msg'));

        $conf = base_setup_config::deploy_info();
        $pagedata['product_key'] = $conf['product_key'];
        return view::make('desktop/login.html', $pagedata);
    }

    public function gen_vcode()
    {
        $vcode = kernel::single('base_vcode');
        $vcode->length(4);
        $vcode->verify_key($this->app->app_id);
        $vcode->display();
    }

    private function __loginLog($msg)
    {
        $log['IP'] = request::getClientIp();
        $log['uname'] = input::get('uname');
        $log['msg'] = $msg;
        $log['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
        $log['type'] = 'shopadmin';
        logger::info('ADMIN_LOGIN:'.var_export($log,1));
    }

    public function login()
    {
        if( pamAccount::isEnableVcode('desktop') )
        {
            if(!base_vcode::verify($this->app->app_id,$_POST['verifycode']))
            {
                $msg = app::get('desktop')->_('验证码不正确！');
                $this->__loginLog($msg);
                $url = url::route('shopadmin', array('ctl' => 'passport', 'act' =>'index', 'url'=>input::get('redirect'), 'msg' => urlencode($msg)));
                echo "<script>location ='$url'</script>";exit;
            }
        }

        try
        {
            kernel::single('desktop_passport')->login(input::get());
            $msg = app::get('desktop')->_('验证成功');
            $this->__loginLog($msg);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();

            $this->__loginLog($msg);

            $url = url::route('shopadmin', array('ctl' => 'passport', 'act' =>'index', 'url'=>input::get('redirect'), 'msg' => urlencode($msg)));
            echo "<script>location ='$url'</script>";exit;
        }

        $params['member_id'] = pamAccount::getAccountId();
        $params['uname'] = pamAccount::getLoginName();
        foreach(kernel::servicelist('desktop_login_listener') as $service)
        {
            $service->listener_login($params);
        }

        if(input::get('remember') === "true")
        {
            setcookie('pam_passport_basic_uname',input::get('uname'),time()+365*24*3600,'/');
        }
        else
        {
            setcookie('pam_passport_basic_uname','',0,'/');
        }

        if($_COOKIE['autologin'] > 0)
        {
            kernel::single('base_session')->set_cookie_expires($_COOKIE['autologin']);
            //如果自动登录，设置cookie过期时间，单位：分
        }

        if($_COOKIE['S']['SIGN']['AUTO'] > 0)
        {
            $minutes = 10*24*60;
            kernel::single('base_session')->set_cookie_expires($minutes);
        }

        header('Location:' .base64_decode(str_replace('%2F','/',urldecode(input::get('redirect')))). $url);
        exit;
    }

    function cross_call(){
        header('Content-Type: text/html;charset=utf-8');
        echo '<script>'.str_replace('top.', 'parent.parent.',base64_decode($_REQUEST['script'])).'</script>';
    }

    function logout($backurl='index.php'){
        $this->begin('javascript:Cookie.dispose("basicloginform_password");Cookie.dispose("basicloginform_autologin");
        location="'.url::route('shopadmin').'"');
        $this->user->login();
        $this->user->logout();

        pamAccount::logout();

        kernel::single('base_session')->destory();
        $this->end('true',app::get('desktop')->_('已成功退出系统,正在转向...'));

    }
}

