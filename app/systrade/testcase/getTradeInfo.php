<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class getTradeInfo extends PHPUnit_Framework_TestCase {

    public function testGet()
    {
        try
        {
            $params = array(
                'tid' =>'1501271430460001',
                'oid' => '1501271430470001',
                //'fields' =>'user_id,shop_id,receiver_name,created_time,orders.title',
                'fields' =>'user_id,shop_id,receiver_name,created_time,orders.oid,orders.title',
            );
            $result = app::get('systrade')->rpcCall('trade.get', $params);
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
