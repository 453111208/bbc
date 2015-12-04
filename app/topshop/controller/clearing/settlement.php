<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_ctl_clearing_settlement extends topshop_controller
{

    /**
     * 结算汇总
     * @return
     */
    public function index()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('商家结算汇总');

        $filter['shop_id'] = $this->shopId;

        $postSend = input::get();

        if($postSend['timearea'])
        {
            $pagedata['timearea'] = $postSend['timearea'];
            $timeArray = explode('-', $postSend['timearea']);
            $filter['settlement_time|than']  = strtotime($timeArray[0]);
            $filter['settlement_time|lthan'] = strtotime($timeArray[1]);
        }
        else
        {
            $filter['settlement_time|than']  = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
            $filter['settlement_time|lthan'] = strtotime(date('Y-m-t  23:59:59', strtotime('-1 month')));
            $pagedata['timearea'] = date('Y/m/01', strtotime('-1 month')).'-'.date('Y/m/t', strtotime('-1 month'));
        }

        if($postSend['settlement_type'])
        {
            $filter['settlement_status'] = $postSend['settlement_type'];
            $pagedata['settlement_type'] = $postSend['settlement_type'];
        }

        //处理翻页数据
        $pagedata['page']   = $page      = $postSend['page'] ? $postSend['page'] : 1;
        $pagedata['limits'] = $pageLimit = 10;
        $objMdlSettlement = app::get('sysclearing')->model('settlement');
        $settlement_list = $objMdlSettlement->getList('*', $filter,($page-1)*$pageLimit,$pageLimit,'settlement_time desc');
        foreach ($settlement_list as $key => $value)
        {
            $settlement_list[$key]['timearea'] = date('Y/m/d',$value['account_start_time']).'-'.date('Y/m/d',$value['account_end_time']);
        }
        $pagedata['settlement_list'] = $settlement_list;
        $count = $objMdlSettlement->count($filter);
        $postSend['token'] = time();
        if($count>0)
        {
            $total = ceil($count/$pageLimit);
        }
        $pagedata['pagers'] = array(
            'link'=>url::action('topshop_ctl_clearing_settlement@index',$postSend),
            'current'=>$page,
            'total'=>$total,
            'token'=>$postSend['token'],
        );

        return $this->page('topshop/clearing/settlement.html', $pagedata);
    }

    /**
     * 结算明细
     * @return
     */
    public function detail()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('商家结算明细');

        $filter['shop_id'] = $this->shopId;

        $postSend = input::get();

        if($postSend['timearea'])
        {
            $pagedata['timearea'] = $postSend['timearea'];
            $timeArray = explode('-', $postSend['timearea']);
            $filter['settlement_time|than']  = strtotime($timeArray[0]);
            $filter['settlement_time|lthan'] = strtotime($timeArray[1]);
        }
        else
        {
            $filter['settlement_time|than']  = time()-3600*24*7;
            $filter['settlement_time|lthan'] = time();
            $pagedata['timearea'] = date('Y/m/d', time()-3600*24*7) . '-' . date('Y/m/d', time());
        }

        if($postSend['settlement_type'])
        {
            $filter['settlement_type'] = $postSend['settlement_type'];
            $pagedata['settlement_type'] = $postSend['settlement_type'];
        }


        //处理翻页数据
        $pagedata['page']   = $page      = $postSend['page'] ? $postSend['page'] : 1;
        $pagedata['limits'] = $pageLimit = 10;
        $objMdlSettleDetail = app::get('sysclearing')->model('settlement_detail');
        $pagedata['settlement_detail_list'] = $objMdlSettleDetail->getList('*', $filter,($page-1)*$pageLimit,$pageLimit,'settlement_time desc');
        $count = $objMdlSettleDetail->count($filter);
        $postSend['token'] = time();
        if($count>0)
        {
            $total = ceil($count/$pageLimit);
        }
        $pagedata['pagers'] = array(
            'link'=>url::action('topshop_ctl_clearing_settlement@detail',$postSend),
            'current'=>$page,
            'total'=>$total,
            'token'=>$postSend['token'],
        );

        return $this->page('topshop/clearing/settlement_detail.html', $pagedata);
    }

}
