<?php
/**
 * 异常订单处理类
 */
class systrade_tradeAbnormal {

    public function __construct()
    {
        $this->objMdlTradeAbnormal = app::get('systrade')->model('tradeabnormal');
    }

    /**
     * 商家创建异常订单申请
     *
     * @param int $tid 申请的订单编号
     * @param string $reason 申请原因
     */
    public function create($tid, $reason)
    {
        $this->__isVerify($tid);

        $insertData['tid'] = $tid;
        $insertData['reason'] = $reason;
        $insertData['created_time'] = time();
        $insertData['modified_time'] = time();

        return $this->objMdlTradeAbnormal->insert($insertData);
    }

    /**
     * 根据ID，获取单条异常订单详情
     *
     * @param int $id
     */
    public function getInfo($id)
    {
        $data = $this->objMdlTradeAbnormal->getRow('*', array('id'=>$id));
    }

    /**
     * 检查单笔订单是否可以进行取消订单申请
     *
     * @param int $tid
     * @return bool true | false
     */
    private function __isVerify($tid)
    {
        //检查订单编号是否已经进行过申请
        $data = $this->objMdlTradeAbnormal->getRow('id', array('tid'=>$tid));
        if( $data )
        {
            throw new \LogicException(app::get('systrade')->_('该订单已申请'));
        }

        //检查订单状态是否可以进行异常订单申请
        $params = array(
            'tid' => $tid,
            'fields' =>'tid,status',
        );
        $tradeData = app::get('systrade')->rpcCall('trade.get', $params);
        if( $tradeData['status'] != 'WAIT_SELLER_SEND_GOODS' )
        {
            throw new \LogicException(app::get('systrade')->_('只有已付款未发货的订单可以申请'));
        }

        return true;
    }
}
