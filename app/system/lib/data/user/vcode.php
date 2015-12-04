<?php
class system_data_user_vcode{
    public $ttl = 3600;//86400;

    public function __construct()
    {
        kernel::single('base_session')->start();
    }

    //随机取6位字符数
    public function randomkeys($length)
    {
        $key = '';
        $pattern = '1234567890';    //字符池
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,9)};    //生成php随机数
        }
        return $key;
    }

    //验证码检查
    public function verify($vcode,$send,$type)
    {
        if(empty($vcode) ) return false;
        $vcodeData = $this->getVcode((string)$send,$type);
        if($vcodeData && $vcodeData['vcode'] == $vcode)
        {
            $data = $this->deleteVcode($vcodeData['account'],$type,$vcodeData);
            return $data;
        }
        else
        {
            return false;
        }
    }

    /*
     * 删除验证码（非物理删除，重新生成一个验证码）
     * */
    public function deleteVcode($account,$type,$vcodeData)
    {
        $vcode = $this->randomkeys(6);
        $vcodeData['vcode'] = $vcode;
        $key = $this->getVcodeKey($account,$type);
        if(defined('WITHOUT_CACHE') && !constant('WITHOUT_CACHE'))
        {
            cacheobject::set($key,$vcodeData,$this->ttl+time());
        }
        else
        {
            base_kvstore::instance('vcode/account')->store($key,$vcodeData,$this->ttl);
        }
        return $vcodeData;
    }

    public function checkVcode($account,$type='signup'){
        $vcodeData = $this->getVcode($account,$type);
        if($vcodeData && !strpos($account,'@')){
            if( $vcodeData['createtime'] == date('Ymd') && $vcodeData['count'] == 3 ){
                throw new \LogicException(app::get('system')->_('每天只能进行3次验证'));
                return false;
            }

            if( time() - $vcodeData['lastmodify'] <= 1 ){
                throw new \LogicException(app::get('system')->_('2分钟发送一次,还没到两分钟则不进行发送'));
                return false;
            }

            if( $vcodeData['createtime'] != date('Ymd') ){
                $vcodeData['count'] = 0;
            }
        }
        return $vcodeData;
    }

    public function getVcodeKey($account,$type='signup')
    {
        return md5($account.$type);
    }

    //获取验证码
    public function getVcode($account,$type='signup')
    {
        $key = $this->getVcodeKey($account,$type);
        if(defined('WITHOUT_CACHE') && !constant('WITHOUT_CACHE'))
        {
            cacheobject::get($key,$vcode);
        }
        else
        {
            base_kvstore::instance('vcode/account')->fetch($key,$vcode);
        }

        return $vcode;
    }

    //短信发送
    public function send_sms($type,$mobile)
    {
        if( !$tmpl = $this->sendtypeToTmpl($type) ) return false;
        $vcodeData = $this->checkVcode($mobile,$type);
        $vcode = $this->randomkeys(6);
        $vcodeData['account'] = $mobile;
        $vcodeData['vcode'] = $vcode;
        $vcodeData['count']  += 1;
        $vcodeData['createtime'] = date('Ymd');
        $vcodeData['lastmodify'] = time();
        $data['vcode'] = $vcode;
        $key = $this->getVcodeKey($mobile,$type);
        $result = messenger::sendSms($mobile,$tmpl,$data);
        if($result['rsp'] == "fail")
        {
            throw new \LogicException(app::get('system')->_('验证码发送失败!'));
        }
        if(defined('WITHOUT_CACHE') && !constant('WITHOUT_CACHE'))
        {
            cacheobject::set($key,$vcodeData,$this->ttl+time());
        }
        else
        {
            base_kvstore::instance('vcode/account')->store($key,$vcodeData,$this->ttl);
        }
        return true;
    }
    //邮件发送
    public function send_email($type,$email,$content){
        if( !$tmpl = $this->sendtypeToTmpl($type) ) return false;
        $vcodeData = $this->checkVcode($email,$type);
        $vcode = $this->randomkeys(6);
        $vcodeData['account'] = $email;
        $vcodeData['vcode'] = $vcode;
        $vcodeData['count']  = 1;
        $vcodeData['createtime'] = date('Ymd');
        $vcodeData['lastmodify'] = time();

        $data['shopname'] = app::get('sysconf')->getConf('site.name');
        $data['vcode'] = $content."&vcode=".$vcode;
        $key = $this->getVcodeKey($email,$type);

        $result = messenger::sendEmail($email,$tmpl,$data);

        if($result['rsp'] == "fail")
        {
            throw new \LogicException(app::get('system')->_('邮件发送失败,请检查邮箱格式是否正确!'));
        }
        if(defined('WITHOUT_CACHE') && !constant('WITHOUT_CACHE'))
        {
            cacheobject::set($key,$vcodeData,3600*24);
        }
        else
        {
            base_kvstore::instance('vcode/account')->store($key,$vcodeData,3600*24);
        }
        return true;
    }

    //短信发送模板设置
    public function sendtypeToTmpl($sendtype){

        $tmpl = false;
        switch($sendtype){
        case 'activation': //激活
            $tmpl = 'account-member';
            break;
        case 'reset': //重置手机号或者邮箱
            $tmpl = 'account-member';
            break;
         case 'unreset': //重置手机号或者邮箱
            $tmpl = 'account-unmember';
            break;
        case 'forgot': //找回密码
            $tmpl = 'account-lostPw';
            break;
        case 'signup': //手机注册
            $tmpl = 'account-signup';
            break;
        }
        return $tmpl;
    }



}
