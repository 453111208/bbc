<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class send_sms extends PHPUnit_Framework_TestCase{


    public function testSend(){

        $content = array(
            'vcode'=>rand(1000,9999),
        );
        $sms = "13816353470";
        $m = messenger::sendSms($sms, 'account-member' , $content);
        if($m['rsp'] == "fail")
        {
            echo $m['err_msg'];
            exit;
        }
        echo "发送短信成功";exit;
    }
}

