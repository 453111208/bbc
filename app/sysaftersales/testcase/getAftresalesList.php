<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class getAftersalesList extends PHPUnit_Framework_TestCase {

    public function testGetAftersalesList()
    {
        try
        {
            $params = array(
                'user_id' =>'1',
                'fields' => 'aftersales_bn,aftersales_type,tid,oid,sku',
            );
            $result = app::get('sysaftersales')->rpcCall('aftersales.list.get', $params);
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
