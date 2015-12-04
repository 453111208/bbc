<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class systrade_data_trade_log{

    /**
     * 添加订单日志
     * @params array 日志数组
     * @return bool
     */
    public function addLog(&$sdf)
    {
        $sdf['log_time'] = time();
        return app::get('systrade')->model('log')->save($sdf);
    }

}
