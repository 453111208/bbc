<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class configx extends PHPUnit_Framework_TestCase
{
    protected $conn = null;

    public function setUp()
    {
    }

	/**
	 * 娴嬭瘯parse comments
	 *
	 * @return void
	 */
    public function testConfigWrite()
    {
        $configWrite = new base_setup_config();
        $configWrite->overwrite = true;

        $configs = [
            'database.host' => config::get('database.host'),
            'database.database' => config::get('database.database'),
            'database.username' => config::get('database.username'),
            'database.password' => config::get('database.password'),
        ];
        
        $configWrite->write($configs);
    }
}