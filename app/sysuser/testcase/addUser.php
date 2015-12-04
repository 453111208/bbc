<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class addUser extends PHPUnit_Framework_TestCase
{
    public function setUp(){
        //$this->model = app::get('base')->model('members');
    }

    public function testRequest(){
        $username = 'username';
        for($i=0; $i<4000; $i++)
        {
            $userInfo['account'] = $username . $i;
            $userInfo['password'] = 'demo123';
            $userInfo['pwd_confirm'] = 'demo123';
            $userId = userAuth::signUp($userInfo['account'], $userInfo['password'], $userInfo['pwd_confirm']);
            echo "注册{$userInfo['account']}，成功！\n";
        }
    }
}
