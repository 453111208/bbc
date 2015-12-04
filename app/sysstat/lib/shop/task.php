<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2014-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * 实现商家报表定时任务
 * @auther gongjiapeng
 * @version 0.1
 * 
 */
class sysstat_shop_task 
{
    /**
     * 得到所有的商家id和新增订单数
     * @param null
     * @return null
     */
    public function newTrade(array $params) 
    {
        $qb = app::get('systrade')->database()->createQueryBuilder();
        $qb->select('count(*) as new_trade ,sum(payment) as new_fee,shop_id as shop_id')
           ->from('systrade_trade')
           ->Where('created_time>='.$qb->createPositionalParameter($params['time_start']))
           ->andWhere('created_time<'.$qb->createPositionalParameter($params['time_end']))
           ->groupBy('shop_id');

        $rows = $qb->execute()->fetchAll();
        return $rows;
    }


    /**
     * 得到所有的商家id和待付款订单数
     * @param null
     * @return null
     */
    public function readyTrade(array $params) 
    {
        $qb = app::get('systrade')->database()->createQueryBuilder();
        $qb->select('count(*) as ready_trade ,sum(payment) as ready_fee,shop_id as shop_id')
           ->from('systrade_trade')
           ->where('status="WAIT_BUYER_PAY"')
           ->andWhere('created_time>='.$qb->createPositionalParameter($params['time_start']))
           ->andWhere('created_time<'.$qb->createPositionalParameter($params['time_end']))
           ->groupBy('shop_id');

        $rows = $qb->execute()->fetchAll();
        return $rows;
    }

    /**
     * 得到所有的商家id和以付款订单数和已付款的金额
     * @param null
     * @return null
     */
    public function alreadyTrade(array $params) 
    {
        $qb = app::get('systrade')->database()->createQueryBuilder();
        $qb->select('count(*) as alreadytrade ,sum(payment) as alreadyfee,shop_id as shop_id')
           ->from('systrade_trade')
           ->where('status<>"WAIT_BUYER_PAY"')
           ->andWhere('pay_time>='.$qb->createPositionalParameter($params['time_start']))
           ->andWhere('pay_time<'.$qb->createPositionalParameter($params['time_end']))
           ->groupBy('shop_id');

        $rows = $qb->execute()->fetchAll();
        return $rows;
    }


    /**
     * 得到所有的商家id和待发货订单数量
     * @param null
     * @return null
     */
    public function readySendTrade(array $params) 
    {
        $qb = app::get('systrade')->database()->createQueryBuilder();
        $qb->select('count(*) as ready_send_trade ,shop_id as shop_id ,sum(payment) as ready_send_fee')
           ->from('systrade_trade')
           ->where('status="WAIT_SELLER_SEND_GOODS"')
           ->andWhere('pay_time>='.$qb->createPositionalParameter($params['time_start']))
           ->andWhere('pay_time<'.$qb->createPositionalParameter($params['time_end']))
           ->groupBy('shop_id');

        $rows = $qb->execute()->fetchAll();
        return $rows;
    }

    /**
     * 得到所有的商家id和待收货订单数量
     * @param null
     * @return null
     */
    public function alreadySendTrade(array $params) 
    {
        $qb = app::get('systrade')->database()->createQueryBuilder();
        $qb->select('count(*) as already_send_trade ,shop_id as shop_id ,sum(payment) as already_send_fee')
           ->from('systrade_trade')
           ->where('status="WAIT_BUYER_CONFIRM_GOODS"')
           ->andWhere('consign_time>='.$qb->createPositionalParameter($params['time_start']))
           ->andWhere('consign_time<'.$qb->createPositionalParameter($params['time_end']))
           ->groupBy('shop_id');

        $rows = $qb->execute()->fetchAll();
        return $rows;
    }


    /**
     * 得到所有的商家id和已完成订单数量
     * @param null
     * @return null
     */
    public function completeTrade(array $params) 
    {
        $qb = app::get('systrade')->database()->createQueryBuilder();
        $qb->select('count(*) as complete_trade ,shop_id as shop_id ,sum(payment) as complete_fee')
           ->from('systrade_trade')
           ->where('status="TRADE_FINISHED"')
           ->andWhere('end_time>='.$qb->createPositionalParameter($params['time_start']))
           ->andWhere('end_time<'.$qb->createPositionalParameter($params['time_end']))
           ->groupBy('shop_id');

        $rows = $qb->execute()->fetchAll();
        return $rows;
    }


    /**
     * 得到所有的商家id和已取消的订单数量
     * @param null
     * @return null
     */
    public function cancleTrade(array $params) 
    {
        $qb = app::get('systrade')->database()->createQueryBuilder();
        $qb->select('count(*) as cancle_trade ,shop_id as shop_id ,sum(payment) as cancle_fee')
           ->from('systrade_trade')
           ->where($qb->expr()->orX('status="TRADE_CLOSED"', 'status="TRADE_CLOSED_BY_SYSTEM"'))
           ->andWhere('end_time>='.$qb->createPositionalParameter($params['time_start']))
           ->andWhere('end_time<'.$qb->createPositionalParameter($params['time_end']))
           ->groupBy('shop_id');
        $rows = $qb->execute()->fetchAll();
        return $rows;
    }

    /**
     * 得到所有的商家id和热门商品
     * @param null
     * @return null
     */
    public function hotGoods(array $params)
    {

        $qb = app::get('systrade')->database()->createQueryBuilder();
        $qb->select('sum(num) as itemnum ,shop_id as shop_id,bn as bn,title as title,item_id as item_id,pic_path as pic_path,sum(payment) as amountprice')
           ->from('systrade_order')
           ->where('status<>"WAIT_BUYER_PAY"')
           ->andWhere('pay_time>='.$qb->createPositionalParameter($params['time_start']))
           ->andWhere('pay_time<'.$qb->createPositionalParameter($params['time_end']))
           ->groupBy('item_id');

        $rows = $qb->execute()->fetchAll();
        return $rows;

    }


}
