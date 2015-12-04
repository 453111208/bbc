<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class tradeFinish extends PHPUnit_Framework_TestCase {

    public function testGet()
    {
        try
        {
            $tid = '1508211523330001';
            $updateData['consign_time'] = time();
            $updateData['pay_time'] = time();
            $updateData['end_time'] = time();
            $updateData['status'] = 'TRADE_FINISHED';
            app::get('systrade')->model('trade')->update($updateData,['tid'=>$tid]);

            $orderUpdateData['pay_time'] = time();
            $orderUpdateData['consign_time'] = time();
            $orderUpdateData['end_time'] = time();
            $orderUpdateData['status'] = 'TRADE_FINISHED';
            app::get('systrade')->model('order')->update($updateData,['tid'=>$tid]);
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
