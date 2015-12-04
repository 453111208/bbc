<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class keys extends PHPUnit_Framework_TestCase
{
    public function setUp(){
    }

    public function testRequest(){
        kernel::single('sysopen_key')->create(1, 'openstandard');
    //  var_dump(app::get('sysopen')->model('keys')->getRow('*', ['shop_id'=>99]));
    }

}
