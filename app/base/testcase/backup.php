<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class backup extends PHPUnit_Framework_TestCase
{
    protected $conn = null;

    public function setUp()
    {
    }

	/**
	 * test
	 *
	 * @return void
	 */
    public function testConfigWrite()
    {
        $oBackup = kernel::single('desktop_system_backup');

        $params = ['dirname' => '20150520160836', 'appname' => 'syscategory'];
        
        var_dump($oBackup->start_backup_sdf($params,$nexturl));
        echo $nexturl;
    }
}