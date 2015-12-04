<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class aftersalesApply extends PHPUnit_Framework_TestCase {

    public function testAftersalesApply()
    {
        try
        {
            $params = array(
                'tid' =>'1501271430460001',
                'oid' => '1501271430470001',
                'shop_id' => '2',
                'reason' =>'其他原因',
            );
            $result = app::get('sysaftersales')->rpcCall('aftersales.apply', $params);
            print_r($result);
            exit;
        }
        catch (\LogicException $e)
        {
            // 逻辑处理
            echo $e->getMessage();
        }
        catch (\RunException $e)
        {
            // 运行时错误处理
            echo $e->getMessage();
        }
        exit;
    }
}
