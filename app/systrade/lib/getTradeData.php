<?php
/**
 * 获取交易数据集合类
 */
class systrade_getTradeData {

    public function __construct()
    {
        $this->objMdlTrade = app::get('systrade')->model('trade');
        $this->objMdlOrder = app::get('systrade')->model('order');
    }

    /**
     * 获取单笔订单数据
     *
     * @param string $fields 需要返回的约束字段
     * @param int $tid 订单编号
     * @param int|array $oid 子订单编号
     */
    public function getTradeInfo($fields, $tid, $oid = null, $filter)
    {
        $filter['tid'] = $tid;
        $tradeInfo = $this->objMdlTrade->getRow($fields['rows'],$filter);
        if( !$tradeInfo ) return array();

        if( !isset($fields['extends']['orders']) ) return $tradeInfo;

        $ordersFilter['tid'] = $tid;
        if( $oid )
        {
            $ordersFilter['oid'] = $oid;
        }

        if( strrpos($fields['rows'],'buyer_rate') )
        {
            $fields['extends']['orders'] = $fields['extends']['orders'].',aftersales_status,status,end_time,buyer_rate';//临时
        }

        $ordersFields = $fields['extends']['orders'];
        $orders = $this->objMdlOrder->getList($ordersFields.',oid', $ordersFilter);
        if( $fields['extends']['activity'] && $orders )
        {
            $oids = array_column($orders,'oid');
            $promotionActivityData = app::get('systrade')->model('promotion_detail')->getList('promotion_tag,oid',['promotion_type'=>'activity','oid'=>$oids]);
            //一个子订单只可参加一次标签促销活动
            $promotionActivityData = array_bind_key($promotionActivityData,'oid');
        }
        foreach( $orders as $k=>$value )
        {
            if( !$value['aftersales_status'] && $value['buyer_rate'] == '0' && $value['status'] == 'TRADE_FINISHED' && (time() - $value['end_time']) < (15*24*3600) )
            {
                $orders[$k]['buyer_rate'] = '0';
                $tradeInfo['buyer_rate'] = '0';
            }
            else
            {
                $orders[$k]['buyer_rate'] = '1';
            }

            if( $promotionActivityData[$value['oid']]['promotion_tag'] )
            {
                $orders[$k]['promotion_tag'] = $promotionActivityData[$value['oid']]['promotion_tag'];
            }
        }
        $tradeInfo['orders'] = $orders;

        return $tradeInfo;
    }

    /**
     * 获取订单列表数据
     *
     * @param string $fields 需要返回的字段
     * @param array  $filter 查询的条件
     * @param array  $pages  分页
     * @param array  $orderBy 排序参数
     *
     * @return array 返回查询到的数据，没有查询到则返回空数组
     */
    public function getTradeList($fields, $filter, $pages, $orderBy)
    {
        $tradeList = $this->objMdlTrade->getList($fields['rows'], $filter, $pages['offset'], $pages['limit'], $orderBy);
        if( empty($tradeList) ) return array();

        return $tradeList;
    }

}
