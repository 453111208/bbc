<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class traderateAdd extends PHPUnit_Framework_TestCase {

    public function testTraderateAdd()
    {
        try
        {
            $rate_data[0] = array(
                 'oid' => '1503161149140001',
                 'result' =>'good',
                 'content' => '好评',
                 'anony' => 0,
            );
            $rate_data[1] = array(
                 'oid' => '1503161149150001',
                 'result' =>'bad',
                 'content' => '差评，没有一周就坏了！',
                 'anony' => 0,
            );
            $rate_data[2] = array(
                 'oid' => '1503161149160001',
                 'result' =>'neutral',
                 'content' => '中评，性价比一般，距离一般！一分钱一分货',
                 'anony' => 0,
            );

            $params = array(
                'tid' =>'1503161149130001',
                'rate_data' => json_encode($rate_data),
                'tally_score' => 4,
                'attitude_score' => 5,
                'delivery_speed_score' =>4,
                'logistics_service_score' =>5,
            );
            $result = app::get('sysrate')->rpcCall('rate.add', $params);
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
