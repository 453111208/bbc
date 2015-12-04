<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topm_ctl_passport extends topm_controller
{

    public function __construct()
    {
        parent::__construct();
        kernel::single('base_session')->start();
        $this->setLayoutFlag('cart');
        $this->passport = kernel::single('topm_passport');
    }

    public function signin()
    {
        $next_page = $this->__getFromUrl();

        if (kernel::single('pam_trust_user')->enabled())
        {
            $trustInfoList = kernel::single('pam_trust_user')->getTrustInfoList('wap', 'topm_ctl_trustlogin@callBack');
        }

        $isShowVcode = userAuth::isShowVcode('login');
        return $this->page('topm/passport/signin/signin.html', compact('trustInfoList','isShowVcode','next_page'));
    }

    public function signup()
    {
        //如果已登录则跳转到退出页
        if( userAuth::check() ) $this->logout();
        $pagedata['next_page'] = $this->__getFromUrl();
        return $this->page('topm/passport/signup/signup.html', $pagedata);
    }

    public function license()
    {
        $pagedata['title'] = "用户注册协议";
        $licence = app::get('sysuser')->getConf('sysconf_setting.wap_license');
        if($licence)
        {
            $pagedata['license'] = $licence;
        }
        else
        {
            $pagedata['license'] = app::get('sysuser')->getConf('sysuser.register.setting_user_license');
        }
        return $this->page('topm/passport/signup/license.html', $pagedata);
    }

    //登陆
    public function login()
    {
        $verifycode = input::get('verifycode');
        if( userAuth::isShowVcode('login') )
        {
            if( !input::get('key') || empty($verifycode) || !base_vcode::verify(input::get('key'), $verifycode))
            {
                $msg = app::get('topm')->_('验证码填写错误');
                return $this->splash('error',$url,$msg,true);
            }
        }

        try
        {
            if (userAuth::attempt(input::get('account'), input::get('password')))
            {
                $url = $this->__getFromUrl();
                return $this->splash('success',$url,$msg,true);
            }
        }
        catch(Exception $e)
        {
            userAuth::setAttemptNumber();
            if( userAuth::isShowVcode('login') )
            {
                $url = url::action('topm_ctl_passport@signin');
            }
            $msg = $e->getMessage();
            return $this->splash('error',$url,$msg,true);
        }
    }

    //注册
    public function create()
    {
        $data = utils::_filter_input(input::get());
        $vcode = $data['vcode'];
        $codyKey = $data['key'];
        $verifycode = $data['verifycode'];
        $userInfo = $data['pam_user'];

        try
        {
            $accountType = kernel::single('pam_tools')->checkLoginNameType($userInfo['account']);
            if($accountType == "mobile")
            {
                $vcodeData=userVcode::verify($vcode,$userInfo['account'],'signup');

                if(!$vcodeData)
                {
                    throw new \LogicException(app::get('topm')->_('手机验证码错误'));
                }
            }
            else
            {
                if( empty($verifycode) || !base_vcode::verify($codyKey,$verifycode) )
                {
                    throw new \LogicException(app::get('topm')->_('验证码填写错误'));
                }
            }
            //检测注册协议是否被阅读选中
            if(!input::get('license'))
            {
                throw new \LogicException(app::get('topm')->_('请阅读并接受会员注册协议'));
            }
            $userId = userAuth::signUp($userInfo['account'], $userInfo['password'], $userInfo['pwd_confirm']);
            userAuth::login($userId, $userInfo['account']);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',$url,$msg,true);
        }

        $url = $this->__getFromUrl();
        return $this->splash('success', $url, null, true);
    }
    //退出
    public function logout()
    {
        userAuth::logout();
        return redirect::action('topm_ctl_passport@signin');
    }

    //检查是否已经注册
    public function checkLoginAccount()
    {
        $signAccount = utils::_filter_input(input::get());

        try
        {
            $loginName = $signAccount['pam_user']['account'];
            $data = userAuth::getAccountInfo($loginName);
            if($data)
            {
                throw new \LogicException('该用户名已被使用');
            }
            $json['needVerify'] = kernel::single('pam_tools')->checkLoginNameType($loginName);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error', null, $msg, true);
        }
        return response::json($json);
    }

    //前端注册验证码的发送
    public function sendVcode()
    {
        $postData = utils::_filter_input(input::get());
        $accountType = kernel::single('pam_tools')->checkLoginNameType($postData['uname']);

        try
        {
            $this->passport->sendVcode($postData['uname'],$postData['type']);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        if($accountType == "email")
        {
            return $this->splash('success',null,"邮箱验证链接已经发送至邮箱，请登录邮箱验证");
        }
        else
        {
            return $this->splash('success',null,"验证码发送成功");
        }
    }

            //找回密码第一步
    public function findPwd()
    {
        return $this->page('topm/passport/forgot/forgot.html');
    }

    //找回密码第二步
    public function findPwdTwo()
    {
        $postData = utils::_filter_input(input::get());
        if($postData)
        {
            $loginName = $postData['username'];
            $data = userAuth::getAccountInfo($loginName);
            if($data)
            {
                if( (!empty($data['email']) && $data['email_verify']) || !empty($data['mobile']))
                {
                    $send_status = 'true';
                }
                else
                {
                    $send_status = 'false';
                }
                $pagedata['send_status'] = $send_status;
                $pagedata['data'] = $data;
                return view::make('topm/passport/forgot/two.html', $pagedata);
            }
        }
        $url = url::action('topm_ctl_passport@findPwd');
        $msg = app::get('topm')->_('账户不存在');
        return $this->splash('error',$url,$msg);
    }

    //找回密码第三步
    public function findPwdThree()
    {

        $postData = utils::_filter_input(input::get());
        $vcode = $postData['vcode'];
        $loginName = $postData['uname'];
        $sendType = $postData['type'];
        $accountType = kernel::single('pam_tools')->checkLoginNameType($loginName);
        try
        {
            $vcodeData=userVcode::verify($vcode,$loginName,$sendType);
            if(!$vcodeData)
            {
                throw new \LogicException('验证码输入错误');
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        $userInfo = userAuth::getAccountInfo($loginName);
        $key = userVcode::getVcodeKey($loginName ,$sendType);
        $userInfo['key'] = md5($vcodeData['vcode'].$key.$userInfo['user_id']);

        $pagedata['data'] = $userInfo;
        $pagedata['account'] = $loginName;
        if($accountType == "email")
        {
            return $this->page('topm/passport/forgot/email_three.html', $pagedata);
        }
        else
        {
            return $this->page('topm/passport/forgot/three.html', $pagedata);
        }
    }
    //找回密码第四步
    public function findPwdFour()
    {
        $postData = utils::_filter_input(input::get());
        $userId = $postData['userid'];
        $account = $postData['account'];

        $vcodeData = userVcode::getVcode($account,'forgot');
        $key = userVcode::getVcodeKey($account,'forgot');

        if($account !=$vcodeData['account']  || $postData['key'] != md5($vcodeData['vcode'].$key.$userId) )
        {
            $msg = app::get('topm')->_('页面已过期,请重新找回密码');
            return $this->splash('failed',null,$msg,true);
        }

        $data['type'] = 'reset';
        $data['new_pwd'] = $postData['password'];
        $data['user_id'] = $postData['userid'];
        $data['confirm_pwd'] = $postData['confirmpwd'];
        try
        {
            app::get('topm')->rpcCall('user.pwd.update',$data,'buyer');

        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            $url = url::action('topm_ctl_passport@findPwd');
            return $this->splash('error',$url,$msg,true);
        }
        $msg = "修改成功";
        $url = url::action('topm_ctl_passport@login');
        return $this->splash('success',$url,$msg,true);
    }

    private function __getFromUrl()
    {
        $url = input::get('next_page', request::server('HTTP_REFERER'));
        if( !is_null($url) )
        {
            if( strpos($url, 'passport') )
            {
                return url::action('topm_ctl_default@index');
            }
            return $url;
        }else{
            return url::action('topm_ctl_default@index');
        }
    }
}
