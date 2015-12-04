<?php

class sysuser_passport
{

    public $userId = null;

    public $userName = null;

    public function __construct()
    {
        $this->app = app::get('sysuser');
    }

    /**
     * 根据会员ID获取用户名，返回优先级为用户名,手机号,邮箱
     *
     * @param int $userId 会员ID
     *
     * @return array ['user_id'=>$value]
     */
    public function getLoginName($userId)
    {
        $filter['user_id'] = $userId;
        $objPamMdlUser = app::get('sysuser')->model('account');
        $data = $objPamMdlUser->getList('user_id,login_account,email,mobile',$filter);
        foreach( (array)$data as $row)
        {
            $name = $row['login_account'] ? $row['login_account'] : ($row['mobile'] ? $row['mobile'] : $row['email']);
            $result[$row['user_id']] = $name;
        }
        return $result;
    }


    /**
     * 会员中心安全中心登陆验证
     *
     * @param array $data 登录用户名和密码
     *
     * @return bool
     */
    public function checkLoginPassword($userId, $password)
    {
        //检查数据安全
        if( empty($password))
        {
            throw new \LogicException(app::get('sysuser')->_('请输入密码!'));
            return false;
        }
        $account = app::get('sysuser')->model('account')->getRow('login_password',array('user_id'=>$userId));
        if( !$account )
        {
            throw new \LogicException(app::get('sysuser')->_('用户信息异常!'));
        }

        if(!pam_encrypt::check($password, $account['login_password']))
        {
            throw new \LogicException(app::get('sysuser')->_('密码填写错误！'));
        }
        return true;
    }


    /**
     * @brief 验证登陆
     *
     * @param string $loginName 登录名
     * @param string $password  密码
     *
     * @return bool
     */
    public function login($loginName, $password)
    {
        $userId = $this->verifyLogin($loginName, $password);
        //设置存储登录状态
        $this->loginAfter($userId);
        return $userId;
    }

    /**
     * 会员中心安全中心登陆验证
     *
     * @param array $data 登录用户名和密码
     *
     * @return bool
     */
    public function validate($user_name, $password)
    {
        //检查数据安全
        $data = utils::_filter_input($data);

        if( empty($data['password']) )
        {
            throw new \LogicException(app::get('sysuser')->_('请输入密码!'));
            //return false;
        }
        $account = app::get('sysuser')->model('account')->getRow('login_password',array('user_id'=>$userId));
        if( !$account )
        {
            throw new \LogicException(app::get('sysuser')->_('用户信息异常!'));
        }

        if(!pam_encrypt::check($data['password'], $account['login_password']))
        {
            throw new \LogicException(app::get('sysuser')->_('密码填写错误！'));
        }
        return true;
    }


    public function loginAfter($userId)
    {
        $objPoints = kernel::single('sysuser_data_user_points');
        $result = $objPoints->pointExpiredCount($userId);
    }

    /**
     * 验证登录的用户名和密码是否一致
     *
     * @param string $loginName 登录名
     * @param string $password  密码
     *
     * @return int $userId
     */
    public function verifyLogin($loginName, $password )
    {
        /*$validator = validator::make(
            ['loginName' => $loginName , 'password' => $password],
            ['loginName' => 'required'       , 'password' => 'required'],
            ['loginName' => '请输入账号!' , 'password' => '请输入密码!']
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();
            foreach( $messages as $error )
            {
                throw new LogicException( $error[0] );
            }
        }*/

        $accountType = kernel::single('pam_tools')->checkLoginNameType($loginName);

        $filter = array($accountType=>trim($loginName));
        $account = app::get('sysuser')->model('account')->getRow('user_id,login_password',$filter);

        if( !$account )
        {
            throw new \LogicException(app::get('sysuser')->_('该账号未注册'));
        }

        if ( !pam_encrypt::check($password, $account['login_password']))
        {
            throw new \LogicException(app::get('sysuser')->_('用户名或密码错误'));
        }

        return $account['user_id'];
    }

    /**
     * 注册一个新的账号
     *
     * @param array $data 注册的数据
     *
     * @return bool
     */
    public function signupUser($data)
    {
        //检查数据安全
        $data = utils::_filter_input($data);

        $objMdlaccount = app::get('sysuser')->model('account');
        $objMdlUser = app::get('sysuser')->model('user');
       
        $accountUser = $this->__preAccountUser($data);

        $db = app::get('sysuser')->database();
        $db->beginTransaction();
        try
        {
            if( !$userId = $objMdlaccount->insert($accountUser) )
            {
                throw new \LogicException('会员数据保存错误');
            }

            $userData = $this->__preUser($userId, $data);
            if( !$objMdlUser->insert($userData) )
            {
                throw new \LogicException('会员数据保存错误');
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return $userId;
    }



    /**
     * @brief 处理注册账号的数据
     *
     * @param array $data
     *
     * @return array
     */
    private function __preAccountUser($data)
    {
        $type = kernel::single('pam_tools')->checkLoginNameType($data['account']);

        //检查注册的账号是否合法
        $this->checkSignupAccount(trim($data['account']),$type);
        //检查注册密码是否一致
        $this->checkPwd($data['password'], $data['pwd_confirm']);

        $account[$type] = trim($data['account']);
        $account['createtime'] = time();
        $account['modified_time'] = time();

        $account['login_password'] = pam_encrypt::make($data['password']);

        $objMdlUserGrade = app::get('sysuser')->model('user_grade');
        $grade = $objMdlUserGrade->getRow('grade_id',array('default_grade'=>1));
        $account['grade_id'] =$grade['grade_id'];

        return $account;
    }

    /**
     * 预处理注册会员基础信息
     *
     * @param int $userId
     * @param array $data
     *
     * @return array
     */
    private function __preUser($userId, $data)
    {
        $user['user_id'] = $userId;
        $user['reg_ip'] = $data['reg_ip'];
        $user['regtime'] = time();
        return $user;
    }

    /**
     * 获取当前会员用户基本信息
     *
     * @param int $userId 如果userId不存在则返回当前会员用户信息,存在返回指定
     *
     * @return array
     */
    public function memInfo($userId)
    {
        if( !$this->memInfo[$userId] )
        {
            $point = app::get('sysuser')->model('user_points')->getRow('point_count',array('user_id'=>$userId));
            $account = app::get('sysuser')->model('account')->getRow('login_account,email,mobile,login_type',array('user_id'=>$userId));
            $sysUserInfo = app::get('sysuser')->model('user')->getRow('*',array('user_id'=>$userId));
            $sysUserGrade = app::get('sysuser')->model('user_grade')->getRow('grade_name,grade_id',array('grade_id'=>$sysUserInfo['grade_id']));

            $memInfo = ['userId' => $userId,
                        'addr' => $sysUserInfo['addr'],
                        'area' => $sysUserInfo['area'],
                        'login_account' => $account['login_account'],
                        'email' => $account['email'],
                        'mobile' => $account['mobile'],
                        'login_type' => $account['login_type'],
                        'name' => $sysUserInfo['name'],
                        'username' => $sysUserInfo['username'],
                        'birthday' => $sysUserInfo['birthday'],
                        'reg_ip' => $sysUserInfo['reg_ip'],
                        'regtime' => $sysUserInfo['regtime'],
                        'sex' => $sysUserInfo['sex'],
                        'point' => $point['point_count'] ? $point['point_count'] : 0,
                        'experience' => $sysUserInfo['experience'] ? $sysUserInfo['experience'] : 0 ,
                        'grade_id' => $sysUserGrade['grade_id'] ? $sysUserGrade['grade_id'] : 0,
                        'grade_name' => $sysUserGrade['grade_name'] ? $sysUserGrade['grade_name'] : "注册会员",
                        'email_verify' => $sysUserInfo['email_verify']];
            $this->memInfo[$userId] = $memInfo;
        }
        return $this->memInfo[$userId];
    }

    /**
     * 检查密码是否合法，密码是否一致(注册，找回密码，修改密码)调用
     *
     * @params string $pwd  密码
     * @params string $pwdConfirm 确认密码
     *
     * @return bool
     */
    public function checkPwd($pwd, $pwdConfirm){
        $validator = validator::make(
            ['password' => $pwd , 'password_confirmation' => $pwdConfirm],
            ['password' => 'min:6|max:20|confirmed'],
            ['password' => '密码长度不能小于6位!|密码长度不能大于20位!|输入的密码不一致!']
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();
            foreach( $messages as $error )
            {
                throw new LogicException( $error[0] );
            }
        }

        return true;
    }//end function


    /**
     * 手机或者邮箱注册，在没有用户名的情况下，需要调用此方法设置用户名
     *
     * @param string $account 设置的用户名
     *
     * @return bool
     */
    public function setAccount($account)
    {
        $userName = $account['user_name'];
        $type = kernel::single('pam_tools')->checkLoginNameType($userName);
        $this->checkSignupAccount($account['user_name'],$type);
        $data['user_id'] = $account['user_id'];
        $data[$type] = $account['user_name'];
        $pamUserModel = app::get('sysuser')->model('account');
        if( !$userId = $pamUserModel->save($data) )
        {
            throw new \LogicException(app::get('cksysuser')->_('修改失败'));
        }
        return true;
    }

    /**
     * 手机或者邮箱注册，在有用户名的情况下,解绑邮箱手机
     *
     * @param string $account 设置的用户名
     *
     * @return bool
     */
    public function unSetAccount($account)
    {
        $userName = $account['user_name'];
        $type = kernel::single('pam_tools')->checkLoginNameType($userName);

        $data['user_id'] = $account['user_id'];
        $data[$type] = '';
        if($type=='email')
        {
            $userData['user_id'] = $account['user_id'];
            $userData['email_verify'] = 0;
        }
        $sysuserAccountModel = app::get('sysuser')->model('account');
        $sysuserUserModel = app::get('sysuser')->model('user');
        $db = app::get('sysuser')->database();
        $db->beginTransaction();
        try
        {
            if($type=='email')
            {
                $sysuserAccountModel->save($data);
                $sysuserUserModel->save($userData);
            }
            $sysuserAccountModel->save($data);
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }

    /**
     * @brief 检查注册账号合法性
     *
     * @param $account 账号
     *
     * @return bool
     */
    public function checkSignupAccount($account,$type){
        if( empty($account) )
        {
            throw new \LogicException(app::get('sysuser')->_('请输入用户名'));
        }

        //获取到注册时账号类型
        switch( $type )
        {
            case 'login_account':
                if( strlen(trim($account))< 4 )
                {
                    throw new \LogicException($this->app->_('登录账号最少4个字符'));
                }
                else if( strlen($account) > 100 )
                {
                    throw new \LogicException($this->app->_('登录账号过长，请换一个重试'));
                }

                if( is_numeric($account) )
                {
                    throw new \LogicException($this->app->_('登录账号不能全为数字'));
                }

                if(!preg_match('/^[^\x00-\x2d^\x2f^\x3a-\x3f]+$/i', trim($account)) )
                {
                    throw new \LogicException($this->app->_('该登录账号包含非法字符'));
                }
                $message = $this->app->_('该账号已经被占用，请换一个重试');
                break;
            case 'email':
                if(!preg_match('/^(?:[a-z\d]+[_\-\+\.]?)*[a-z\d]+@(?:([a-z\d]+\-?)*[a-z\d]+\.)+([a-z]{2,})+$/i',trim($account)) )
                {
                    throw new \LogicException($this->app->_('邮件格式不正确'));
                }
                $message = $this->app->_('该邮箱已被注册，请更换一个');
                break;
            case 'mobile':
                $message = $this->app->_('该手机号已被注册，请更换一个');
                break;
        }

        //判断账号是否存在
        if( $this->isExistsAccount($account) )
        {
            throw new \LogicException($message);
            return false;
        }
        return true;
    }//end function

    /**
     * @brief 判断前台用户名是否存在
     *
     * @param string $account
     *
     * @return
     */
    public function isExistsAccount($account,$userId = null)
    {
        if( empty($account) )
        {
            throw new \LogicException(app::get('sysuser')->_('验证数据不能为空'));
            return false;
        }

        $type = kernel::single('pam_tools')->checkLoginNameType($account);
        $filter[$type] = $account;
        if($userId)
        {
             $filter['user_id|noequal'] = $userId;
        }

        $objMdlaccount = app::get('sysuser')->model('account');
        $flag = $objMdlaccount->getRow('user_id',$filter);
        return $flag ? true : false;
    }

    /**
     * 修改密码，需要使用旧密码进行修改，新密码和确认密码需要一致
     *
     * @param int $userId 修改密码的user_id
     * @param array $data
     *
     * @return bool
     */
    public function modifyPwd($data)
    {
        $data = utils::_filter_input($data);

        $pamUserModel = app::get('sysuser')->model('account');
        $account = $pamUserModel->getRow('modified_time,createtime,login_password,login_account',array('user_id'=>$data['user_id']));
        if( !$account )
        {
            throw new \LogicException(app::get('sysuser')->_('会员信息有误'));
        }

        if($data['type'] == "update" && $data['old_pwd'])
        {
            if(!pam_encrypt::check($data['old_pwd'], $account['login_password']))
            {
                throw new \LogicException(app::get('sysuser')->_('原密码错误'));
            }
        }

        //检查密码合法，是否一致
        $this->checkPwd($data['new_pwd'],$data['confirm_pwd']);

        $pamUserData['login_password']= pam_encrypt::make($data['new_pwd']);
        if($data['uname'])
        {
            $type = kernel::single('pam_tools')->checkLoginNameType($data['uname']);
            $this->checkSignupAccount(trim($data['uname']),$type);
            $pamUserData['login_account'] = $data['uname'];
            $pamUserData['login_type'] = 'common';
        }
        $pamUserData['user_id'] = $data['user_id'];
        $pamUserData['modified_time'] = time();
        if( !$userId = $pamUserModel->save($pamUserData) )
        {
            throw new \LogicException(app::get('sysuser')->_('修改失败'));
        }
        return true;
    }

    /**
     * @brief 后台会员信息修改
     *
     * @param array $data
     *
     * @return bool
     */
    public function saveInfo($data)
    {
            $data['user']['sex'] = $data['user']['sex']=='male' ? 1 :0;
            $data['user']['birthday'] = strtotime($data['user']['birthday']);
            if(empty($data['user']['name']))
            {
                throw new \LogicException(app::get('sysuser')->_('用户昵称不能为空'));
            }
            if( strlen(trim($data['user']['name']))< 4 || strlen(trim($data['user']['name']))>20)
            {
                throw new \LogicException(app::get('sysuser')->_('用户昵称最少4个字符,最多20个字符'));
            }

            $sysdata = array(
                'user_id'=>$data['user']['user_id'],
                'sex'=>$data['user']['sex'],
                'name'=>$data['user']['name'],
                'birthday'=>$data['user']['birthday'],
            );
            if(!app::get('sysuser')->model('user')->save($sysdata))
            {
                throw new \LogicException(app::get('sysuser')->_('修改失败'));
            }
            return true;
    }

    /**
     * @brief 发送验证马
     *
     */
    public function sendVcode($sendType,$uname,$terminal=null)
    {
        if(!$sendType || !$uname)
        {
            throw new \LogicException(app::get('sysuser')->_('请填写正确的手机号码或者邮箱号'));
            return false;
        }
        $login_type = kernel::single('pam_tools')->checkLoginNameType($uname);

        //手机短信验证码
        if($sendType && $login_type=='mobile')
        {
            if( !userVcode::send_sms($sendType,(string)$uname,$terminal) )
            {
                throw new \LogicException(app::get('sysuser')->_('验证码发送失败'));
                return false;
            }
        }
        elseif($sendType && $login_type=='email')
        {
            if( !userVcode::send_email($sendType,(string)$uname,$terminal) )
            {
                throw new \LogicException(app::get('sysuser')->_('邮件发送失败'));
                return false;
            }
        }
        else
        {
            if($login_type=='email')
            {
                throw new \LogicException(app::get('sysuser')->_('请填写正确的邮箱号'));
                return false;
            }
            elseif($login_type=='mobile')
            {
                throw new \LogicException(app::get('sysuser')->_('请填写正确的手机号'));
                return false;
            }
            else
            {
                throw new \LogicException(app::get('sysuser')->_('请填写正确的手机号或者邮箱号'));
                return false;
            }

        }

    }

    public function checkVcode($postData)
    {
        if(!$postData)
        {
            throw new \LogicException(app::get('sysuser')->_('请填写正确的数据'));
        }
        $vcodeData=userVcode::verify($postData['vcode'],$postData['uname'],$postData['sendType']);
        if(!$vcodeData)
        {
            throw new \LogicException(app::get('sysuser')->_('验证码错误'));
        }
        return $vcodeData;
    }

    /**
     * 会员中心信息修改最后一步
     *
     */
    public function saveSetInfo($postData)
    {

        if(!$postData)
        {
            throw new \LogicException(app::get('sysuser')->_('请填写正确的数据'));
            return false;
        }

        if(!userVcode::verify($postData['vcode'],$postData['uname'],$postData['send_type']))
        {
            throw new \LogicException(app::get('sysuser')->_('验证码错误'));
            return false;
        }
        $type = kernel::single('pam_tools')->checkLoginNameType($postData['uname']);
        $userId = pamAccount::getAccountId();

        $db = app::get('sysuser')->database();
        $transaction_status = $db->beginTransaction();

        try
        {
            if($type == 'email' && $userId)
            {
                $data = array(
                    'user_id'=>$userId,
                    'email'=>$postData['uname']
                );
                $user = app::get('sysuser')->model('account')->getRow('user_id',array('email'=>$postData['uname']));
                if($user['user_id'])
                {
                    throw new \LogicException(app::get('sysuser')->_('该邮箱以被绑定'));
                }
            }
            if($type == 'mobile' && $userId)
            {
                $user = app::get('sysuser')->model('account')->getRow('user_id',array('mobile'=>$postData['uname']));
                if($user['user_id'])
                {
                    throw new \LogicException(app::get('sysuser')->_('该手机号以被绑定'));
                }
                $data = array(
                    'user_id'=>$userId,
                    'mobile'=>$postData['uname']
                );
            }

            $objPamMdlUser = app::get('sysuser')->model('account');

            if(!$objPamMdlUser->save($data))
            {
                throw new \LogicException(app::get('sysuser')->_("{$type}认证失败"));
            }
            $db->commit($transaction_status);
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }
}

