<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class send extends PHPUnit_Framework_TestCase{

    public function testSend()
    {
        $content = array(
            'vcode'=>rand(1000,9999),
        );
        $sendTo['sms'] = array('13816351212');
        $sendTo['email'] = array("test@shopex.cn");
        $m = messenger::send($sendTo,'account-member' , $content);
        if($m['rsp'] == "fail")
        {
            echo $m['err_msg'];
        }
        echo "加入队列成功";

    }
}

