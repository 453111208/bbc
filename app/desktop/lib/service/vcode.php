<?php

class desktop_service_vcode {

    public function status()
    {
        pamAccount::setAuthType('desktop');

        $errorCount = pamAccount::getLoginErrorCount();

        //验证码必填是否开启
        $mustVcode = app::get('desktop')->getConf('shopadminVcode');
        if( $mustVcode != 'true' )
        {
            //没开启验证码必填的情况下，错误三次及其以上则需要验证码
            return ($errorCount >= 3) ?  true : false;
        }

        return true;
    }
}

