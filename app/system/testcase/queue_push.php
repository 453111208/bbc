<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class queue_push extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function testPublish(){
        queue::push('cleankvstore', array());
    }
}



