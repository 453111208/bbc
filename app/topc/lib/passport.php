<?php
class topc_passport
{

	/**
	 * 前端注册验证码的发送
	 *
	 * @param  string $account
	 * @return string
	 */
    public function sendVcode($account,$sendType)
    {
        if(!$account)
        {
            throw new \LogicException(app::get('topc')->_('请填写正确的手机号码或者邮箱号'));
        }

        if(!$sendType)
        {
            throw new \LogicException(app::get('topc')->_('参数错误'));
        }

        

        $type = kernel::single('pam_tools')->checkLoginNameType($account);
        if($sendType=='reset')
        {
            $data = userAuth::getAccountInfo($account);
            if($data)
            {
                throw new \LogicException('该'.$type.'已被使用');
            }
        }
        switch($type)
        {
        case "email":
            if( !$this->sendEmailVcode($sendType,$account))
            {
                throw new \LogicException(app::get('topc')->_('邮件发送失败'));
            }
            break;
        case "mobile":
            if( !userVcode::send_sms($sendType,$account) )
            {
                throw new \LogicException(app::get('topc')->_('验证码发送失败'));
            }
            break;
        default:
            throw new \LogicException(app::get('topc')->_('验证码发送失败'));
            break;
        }
        return true;
    }

    public function sendEmailVcode($sendType,$account)
    {
        $userId = userAuth::id();
        switch($sendType)
        {
        case 'activation':
        case 'reset':
            $content = url::action("topc_ctl_member@bindEmail",array('uname'=>$account,'type'=>$sendType,'verify'=>md5($userId)));
            break;
        case 'forgot':
            $content = url::action("topc_ctl_passport@findPwdThree",array('uname'=>$account,'type'=>$sendType,'verify'=>md5($userId)));
            break;
        case 'unreset':
            $content = url::action("topc_ctl_member@unVerifyEmail",array('uname'=>$account,'type'=>$sendType,'verify'=>md5($userId)));
            break;
        case 'signup': //手机注册
            $content = url::action("topc_ctl_passport@findPwdThree",array('uname'=>$account,'type'=>$sendType));
            break;
        }

        if(!userVcode::send_email($sendType,$account,$content))
        {
            return false;
        }
        return true;
    }
}


