<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class send_email extends PHPUnit_Framework_TestCase{

    public function testSend(){

        $content = array(
            'vcode'=>rand(1000,9999),
        );
        $email = "test@shopex.cn";
        $m = messenger::sendEmail($email, 'account-member' , $content);
        if($m['rsp'] == "fail")
        {
            echo $m['err_msg'];
            exit;
        }
        echo "发送邮件成功";exit;

    }
}

