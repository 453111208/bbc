<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2014-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * 实现商家报表返回数据
 * @auther shopex ecstore dev dev@shopex.cn
 * @version 0.1
 * @package sysstat.lib.analysis
 */
class sysstat_shop_taskdata 
{
	public function exec($params)
    { 
        // 得到所有的商家id和热门商品
        $hotGoods = kernel::single('sysstat_shop_task')->hotGoods($params);
        // 得到所有的商家id和新增订单数,新增订单额
        $newTrade = kernel::single('sysstat_shop_task')->newTrade($params);
        // 得到所有的商家id和待付款订单数,待付款订单额
        $readyTrade = kernel::single('sysstat_shop_task')->readyTrade($params);
        // 得到所有的商家id和以付款订单数,以付款订单额
        $alreadyTrade = kernel::single('sysstat_shop_task')->alreadyTrade($params);
        // 得到所有的商家id和待发货订单数量,待发货订单额
        $readySendTrade = kernel::single('sysstat_shop_task')->readySendTrade($params);
        // 得到所有的商家id和待收货订单数量,待收货订单额
        $alreadySendTrade = kernel::single('sysstat_shop_task')->alreadySendTrade($params);
        // 得到所有的商家id和已完成订单数量,已完成订单额
        $completeTrade = kernel::single('sysstat_shop_task')->completeTrade($params);
        // 得到所有的商家id和已取消的订单数量,已取消订单额
        $cancleTrade = kernel::single('sysstat_shop_task')->cancleTrade($params);

        $data = $this->getData($newTrade,$readyTrade,$readySendTrade,$alreadySendTrade,$completeTrade,$cancleTrade,$alreadyTrade,$params);
        $goodsData = $this->getGoodsData($hotGoods,$params);
        $tradeStaticsMdl = app::get('sysstat')->model('trade_statics');
        $itemStaticsMdl = app::get('sysstat')->model('item_statics');
        foreach ($data as $value)
        {
            $filter = array('shop_id'=>$value['shop_id']);
            $rows = app::get('sysstat')->database()->executeQuery('SELECT stat_id FROM sysstat_trade_statics where shop_id= ? and createtime= ?', [$value['shop_id'], $value['createtime']])->fetch();

            if($rows['stat_id'])
            {
                $value['stat_id'] = $rows['stat_id'];
            }
            $tradeStaticsMdl->save($value);
        }


        foreach ($goodsData as $key =>$value)
        {
            $filter = array('shop_id'=>$value['shop_id']);
            $rows = app::get('sysstat')->database()->executeQuery('SELECT item_stat_id FROM sysstat_item_statics where shop_id= ? and createtime= ? and item_id= ?', [$value['shop_id'], $value['createtime'],$value['item_id']])->fetch();

            if($rows['item_stat_id'])
            {
                $value['item_stat_id'] = $rows['item_stat_id'];
            }
            $itemStaticsMdl->save($value);
        }
    }

    /**
     * 获取商品统计数据
     * @param null
     * @return null
     */
    public function getGoodsData($hotGoods,$params)
    {
        foreach ($hotGoods as $key => $value) {
            $goodsData[$value['item_id']]['item_id'] = $value['item_id'];
            $goodsData[$value['item_id']]['shop_id'] = $value['shop_id'];
            $goodsData[$value['item_id']]['title'] = $value['title'];
            $goodsData[$value['item_id']]['pic_path'] = $value['pic_path'];
            $goodsData[$value['item_id']]['amountnum'] = $value['itemnum'];
            $goodsData[$value['item_id']]['amountprice'] = $value['amountprice'];
            $goodsData[$value['item_id']]['createtime'] = $params['time_insert'];
        }
        return $goodsData;
    }
    /**
     * 获取统计数据
     * @param null
     * @return null
     */
    public function getData($newTrade,$readyTrade,$readySendTrade,$alreadySendTrade,$completeTrade,$cancleTrade,$alreadyTrade,$params)
    {
        foreach ($newTrade as $key => $value)
        {
            $newTradeData[$value['shop_id']]['shop_id'] = $value['shop_id'];
            $newTradeData[$value['shop_id']]['new_trade'] = $value['new_trade'];
            $newTradeData[$value['shop_id']]['new_fee'] = $value['new_fee'];
        }
        foreach ($readyTrade as $key => $value)
        {
            $readyTradeData[$value['shop_id']]['shop_id'] = $value['shop_id'];
            $readyTradeData[$value['shop_id']]['ready_trade'] = $value['ready_trade'];
            $readyTradeData[$value['shop_id']]['ready_fee'] = $value['ready_fee'];
        }
        foreach ($readySendTrade as $key => $value)
        {
            $readySendTradeData[$value['shop_id']]['shop_id'] = $value['shop_id'];
            $readySendTradeData[$value['shop_id']]['ready_send_trade'] = $value['ready_send_trade'];
            $readySendTradeData[$value['shop_id']]['ready_send_fee'] = $value['ready_send_fee'];
        }
        foreach ($alreadySendTrade as $key => $value)
        {
            $alreadySendTradeData[$value['shop_id']]['shop_id'] = $value['shop_id'];
            $alreadySendTradeData[$value['shop_id']]['already_send_trade'] = $value['already_send_trade'];
            $alreadySendTradeData[$value['shop_id']]['already_send_fee'] = $value['already_send_fee'];
        }
        foreach ($completeTrade as $key => $value)
        {
            $completeTradeData[$value['shop_id']]['shop_id'] = $value['shop_id'];
            $completeTradeData[$value['shop_id']]['complete_trade'] = $value['complete_trade'];
            $completeTradeData[$value['shop_id']]['complete_fee'] = $value['complete_fee'];
        }
        foreach ($cancleTrade as $key => $value)
        {
            $cancleTradeData[$value['shop_id']]['shop_id'] = $value['shop_id'];
            $cancleTradeData[$value['shop_id']]['cancle_trade'] = $value['cancle_trade'];
            $cancleTradeData[$value['shop_id']]['cancle_fee'] = $value['cancle_fee'];
        }
        foreach ($alreadyTrade as $key => $value)
        {
            $alreadyTradeData[$value['shop_id']]['shop_id'] = $value['shop_id'];
            $alreadyTradeData[$value['shop_id']]['alreadytrade'] = $value['alreadytrade'];
            $alreadyTradeData[$value['shop_id']]['alreadyfee'] = $value['alreadyfee'];
        }
        foreach ($newTradeData as $key => $value)
        {
            $data[$value['shop_id']]['shop_id'] = $value['shop_id'];
            $data[$value['shop_id']]['new_trade'] = $newTradeData[$value['shop_id']]['new_trade']?$newTradeData[$value['shop_id']]['new_trade']:0;
            $data[$value['shop_id']]['new_fee'] = $newTradeData[$value['shop_id']]['new_fee']?$newTradeData[$value['shop_id']]['new_fee']:0;
            $data[$value['shop_id']]['ready_trade'] = $readyTradeData[$value['shop_id']]['ready_trade']?$readyTradeData[$value['shop_id']]['ready_trade']:0;
            $data[$value['shop_id']]['ready_fee'] =$readyTradeData[$value['shop_id']]['ready_fee']?$readyTradeData[$value['shop_id']]['ready_fee']:0;

            $data[$value['shop_id']]['ready_send_trade'] = $readySendTradeData[$value['shop_id']]['ready_send_trade']?$readySendTradeData[$value['shop_id']]['ready_send_trade']:0;
            $data[$value['shop_id']]['ready_send_fee'] = $readySendTradeData[$value['shop_id']]['ready_send_fee']?$readySendTradeData[$value['shop_id']]['ready_send_fee']:0;

            $data[$value['shop_id']]['already_send_trade'] = $alreadySendTradeData[$value['shop_id']]['already_send_trade']?$alreadySendTradeData[$value['shop_id']]['already_send_trade']:0;
            $data[$value['shop_id']]['already_send_fee'] = $alreadySendTradeData[$value['shop_id']]['already_send_fee']?$alreadySendTradeData[$value['shop_id']]['already_send_fee']:0;

            $data[$value['shop_id']]['cancle_trade'] = $cancleTradeData[$value['shop_id']]['cancle_trade']?$cancleTradeData[$value['shop_id']]['cancle_trade']:0;
            $data[$value['shop_id']]['cancle_fee'] = $cancleTradeData[$value['shop_id']]['cancle_fee']?$cancleTradeData[$value['shop_id']]['cancle_fee']:0;

            $data[$value['shop_id']]['complete_trade'] = $completeTradeData[$value['shop_id']]['complete_trade']?$completeTradeData[$value['shop_id']]['complete_trade']:0;
            $data[$value['shop_id']]['complete_fee'] = $completeTradeData[$value['shop_id']]['already_send_fee']?$completeTradeData[$value['shop_id']]['complete_trade']:0;

            $data[$value['shop_id']]['alreadytrade'] = $alreadyTradeData[$value['shop_id']]['alreadytrade']?$alreadyTradeData[$value['shop_id']]['alreadytrade']:0;
            $data[$value['shop_id']]['alreadyfee'] = $alreadyTradeData[$value['shop_id']]['alreadyfee']?$alreadyTradeData[$value['shop_id']]['alreadyfee']:0;
            $data[$value['shop_id']]['createtime'] = $params['time_insert'];
        }
        return $data;
    }

}
