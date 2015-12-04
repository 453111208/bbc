<?php
class systrade_tasks_trade_cancel extends base_task_abstract implements base_interface_task{
    public function exec($params=null)
    {
        $intervalTime = app::get('sysconf')->getConf('trade.cancel.spacing.time');
        $tradeProcess = kernel::single('systrade_trade_process');
        return $intervalTime ? $tradeProcess->cancel($intervalTime*3600) : $tradeProcess->cancel(72*3600);
    }
}
