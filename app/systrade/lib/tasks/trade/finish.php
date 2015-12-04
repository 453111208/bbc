<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class systrade_tasks_trade_finish extends base_task_abstract implements base_interface_task{
    public function exec($params=null)
    {
        $intervalTime = app::get('sysconf')->getConf('trade.finish.spacing.time');
        $tradeProcess = kernel::single('systrade_trade_process');
        return $intervalTime ? $tradeProcess->finish($intervalTime * 86400) : $tradeProcess->finish(604800);
    }
}
