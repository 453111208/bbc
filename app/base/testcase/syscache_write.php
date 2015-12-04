<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class syscache_write extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->handler = new base_syscache_setting;
    }

    public function testFileAdapter()
    {
        
        $fileAdapter = new base_syscache_adapter_filesystem($this->handler);
        for($i=0; $i<1000000; $i++)
        {
            echo $i.PHP_EOL;
            $random = rand(1, 30);
            $fileAdapter->create([$i, $random]);
        }
        
        //var_dump($fileAdapter->_data);
    }


}
