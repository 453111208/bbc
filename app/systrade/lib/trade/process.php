<?php
class systrade_trade_process{

    /**
     *  以前这里的逻辑是直接传一个filter给systrade_data_trade_confirm的generate方法，直接是搜索这个时间段内的所有的等待确认收货的订单。
     *  但是这个方法是单个订单确认收货的，而且和里面一个逻辑有冲突，所以以后改为如下逻辑：
     *  先将所有订单的tid拖出来，循环这些tid，一笔一笔的去自动确认收货
     *  取消订单也是一样的逻辑问题
     *                                           2015.9.9 by Elrond
     */
    public function finish($secondTime)
    {
        $secondTime = time()-$secondTime;

        $params['data']['status'] = "TRADE_FINISHED";
        $params['data']['modified_time'] = time();
        $params['data']['end_time'] = time();
      //$params['filter']['consign_time|sthan'] = $secondTime;
      //$params['filter']['status'] = "WAIT_BUYER_CONFIRM_GOODS";
        $paramsForList['rows']   = 'tid';
        $paramsForList['filter'] = ['consign_time|sthan'=>$secondTime, 'status'=>"WAIT_BUYER_CONFIRM_GOODS"];
        $tidList = kernel::single('systrade_data_trade')->getTradeList($paramsForList, false);

        //当没有订单需要取消的时候，就自动跳完成。
        if($tidList == null) return true;

        foreach($tidList as $tid)
        {
            try{
                $params['filter']['tid'] = $tid['tid'];
                $objTradeConfirm = kernel::single('systrade_data_trade_confirm');
                $result = $objTradeConfirm->generate($params);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();
                logger::info('Complete order(' . $tid['tid']. ') LogicException log automatically:'.$msg);
            }
            catch(Exception $e)
            {
                $msg = $e->getMessage();
                logger::info('Complete order(' . $tid['tid'] . ') error log automatically:'.$msg);
            }
        }
        return true;
    }

    public function cancel($secondTime)
    {
        $minuteTime = $secondTime/60;
        $secondTime = time()-$secondTime;

        $params['data']['cancel_reason'] = "订单未在下单".$minuteTime."分钟内完成支付,被系统自动关闭。";
        $params['data']['status'] = "TRADE_CLOSED_BY_SYSTEM";
        $params['data']['end_time'] = time();

      //$params['filter']['created_time|sthan'] = $secondTime;
      //$params['filter']['status'] ='WAIT_BUYER_PAY';
        $paramsForList['rows']   = 'tid';
        $paramsForList['filter'] = ['created_time|sthan'=>$secondTime, 'status'=>'WAIT_BUYER_PAY'];
        $tidList = kernel::single('systrade_data_trade')->getTradeList($paramsForList, false);
        foreach($tidList as $tid)
        {
            try
            {
                $params['filter']['tid'] = $tid['tid'];
                $objTradeCancel = kernel::single('systrade_data_trade_cancel');
                $result = $objTradeCancel->generate($params);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();
                logger::info('Cancel the order(' . $tid['tid'] . ') LogicException log automatically:'.$msg);
            }

            catch(Exception $e)
            {
                $msg = $e->getMessage();
                logger::info('Cancel the order(' . $tid['tid'] . ') error log automatically:'.$msg);
            }
        }
        return true;
    }
}
