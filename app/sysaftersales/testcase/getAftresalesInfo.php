<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class getAftersalesInfo extends PHPUnit_Framework_TestCase {

    public function testGetAftersalesInfo()
    {
        try
        {
            $params = array(
                'aftersales_bn' =>'150210113443000160',
                'fields' => 'aftersales_bn,aftersales_type,tid,oid,sku',
            );
            $result = app::get('sysaftersales')->rpcCall('aftersales.get', $params);
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
