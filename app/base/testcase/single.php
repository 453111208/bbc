<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

 
class single extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testKernelSingle()
    {
        $a = 1;
        $b = 2;
        $c = 3;
        kernel::single1('abc', $a, $b, $c);
        
    }
    
}
class abc
{
    public function __construct()
    {
        echo 999;
        var_dump(func_get_args());
    }
    
}
