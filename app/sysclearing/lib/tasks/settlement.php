<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysclearing_tasks_settlement extends base_task_abstract implements base_interface_task
{
    // 每个队列执行100条订单信息
    var $limit = 100;
    public function exec($params=null)
    {

        $filter = array(
            'settlement_time|than'=>strtotime(date('Y-m-01 00:00:00', strtotime('-1 month'))),
            'settlement_time|lthan'=>strtotime(date('Y-m-t  23:59:59', strtotime('-1 month'))),
        );
        $objLibMath = kernel::single('ectools_math');
        $objMdlSettlement = app::get('sysclearing')->model('settlement');
        $objMdlSettlementDetail = app::get('sysclearing')->model('settlement_detail');
        $objMdlShop = app::get('sysshop')->model('shop');
        $shopids = $objMdlShop->getList('shop_id');
        foreach($shopids as $v)
        {
            $filter['shop_id'] = $v;
            if($settlementList =  $objMdlSettlementDetail->getList('*',$filter))
            {
                $tradecount = 0;
                $item_fee_amount = array();
                $post_fee_amount = array();
                $refund_fee_amount = array();
                $commission_fee_amount = array();
                $settlement_fee_amount = array();
                foreach($settlementList as $detail)
                {
                    $tradecount += 1;
                    $item_fee_amount[] = $detail['payment'];
                    $post_fee_amount[] = $detail['post_fee'];
                    $refund_fee_amount[] = $detail['refund_fee'];
                    $commission_fee_amount[] = $detail['commission_fee'];
                    $settlement_fee_amount[] = $detail['settlement_fee'];

                }
                $settle['settlement_no'] = date('ym').str_pad($v['shop_id'],6,'0',STR_PAD_LEFT);
                $settle['shop_id'] = $v['shop_id'];
                $settle['tradecount'] = $tradecount;
                $settle['item_fee_amount'] = $objLibMath->number_plus($item_fee_amount);
                $settle['post_fee_amount'] = $objLibMath->number_plus($post_fee_amount);
                $settle['refund_fee_amount'] = $objLibMath->number_plus($refund_fee_amount);
                $settle['commission_fee_amount'] = $objLibMath->number_plus($commission_fee_amount);
                $settle['settlement_fee_amount'] = $objLibMath->number_plus($settlement_fee_amount);
                $settle['settlement_status'] = '1';
                $settle['account_start_time'] = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
                $settle['account_end_time'] = strtotime(date('Y-m-t  23:59:59', strtotime('-1 month')));
                $settle['settlement_time'] = time();
            }
            else
            {
                $settle['settlement_no'] = date('ym').str_pad($v['shop_id'],5,'0',STR_PAD_LEFT);
                $settle['shop_id'] = $v['shop_id'];
                $settle['tradecount'] = 0;
                $settle['item_fee_amount'] = 0;
                $settle['post_fee_amount'] = 0;
                $settle['refund_fee_amount'] = 0;
                $settle['commission_fee_amount'] = 0;
                $settle['settlement_fee_amount'] = 0;
                $settle['settlement_status'] = '1';
                $settle['account_start_time'] = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
                $settle['account_end_time'] = strtotime(date('Y-m-t  23:59:59', strtotime('-1 month')));
                $settle['settlement_time'] = time();
            }
            $objMdlSettlement->save($settle);
        }
    }

 }
