<?php
class topm_ctl_trustlogin extends topm_controller{

    public function __construct()
    {
        parent::__construct();
        $this->setLayoutFlag('passport');
    }

	/**
	 * callback返回页, 同时是bind页面
	 *
	 * @return base_http_response
	 */    
    public function callback()
    {
        $params = input::get();
        $flag = $params['flag'];
        unset($params['flag']);

        // 信任登陆校验
        $userTrust = kernel::single('pam_trust_user');
        $res = $userTrust->authorize($flag, 'web', 'topm_ctl_trustlogin@callBack', $params);
        
        $binded = $res['binded'];

        if ($binded)
        {
            $userId = $res['user_id'];

            userAuth::login($userId);
            return redirect::action('topm_ctl_default@index');
        }
        else
        {
            $pagedata = compact('flag');
            return $this->page('topm/bind.html', $pagedata);
        }
    }

    public function bindDefaultCreateUser()
    {
        $params = input::get();
        $flag = $params['flag'];
        try
        {
            $userId = kernel::single('pam_trust_user')->bindDefaultCreateUser($flag);
            userAuth::login($userId, $loginName);
            
            $url = url::action('topm_ctl_default@index');
            return $this->splash('success', $url, $msg, true);
            
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
    }

    public function bindExistsUser()
    {
        $params = input::get();
        $verifyCode = $params['verifycode'];
        $verifyKey = $params['vcodekey'];
        $loginName = $params['uname'];
        $password = $params['password'];

        if( (!$verifyKey) || $b=empty($verifyCode) || $c=!base_vcode::verify($verifyKey, $verifyCode))
        {
            $msg = app::get('topm')->_('验证码填写错误') ;
            return $this->splash('error', null, $msg, true);
        }

        try
        {
            if (userAuth::attempt($loginName, $password))
            {
                kernel::single('pam_trust_user')->bind(userAuth::id());
                $url = url::action('topm_ctl_default@index');
                return $this->splash('success', $url, $msg, true);
            }
        }
        catch (Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
    }

    public function bindSignupUser()
    {
        $params = input::get();
        $verifyCode = $params['verifycode'];
        $verifyKey =  $params['vcodekey'];
        $loginName = $params['pam_account']['login_name'];
        $password = $params['pam_account']['login_password'];
        $confirmedPassword = $params['pam_account']['psw_confirm'];

        if( !$verifyKey || empty($verifyCode) || !base_vcode::verify($verifyKey, $verifyCode))
        {
            $msg = app::get('topm')->_('验证码填写错误') ;
            return $this->splash('error', null, $msg, true);
        }

        try
        {
            $userId = userAuth::signUp($loginName, $password, $confirmedPassword);
            userAuth::login($userId, $loginName);
            kernel::single('pam_trust_user')->bind(userAuth::id());
            
            $url = url::action('topm_ctl_default@index');
            return $this->splash('success', $url, $msg, true);
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
    }
}
