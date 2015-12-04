<?php
/**
 *  处理退款
 */
class sysaftersales_refunds {

    public function __construct()
    {
        $this->objMdlRefunds = app::get('sysaftersales')->model('refunds');
        $this->objMath = kernel::single('ectools_math');
    }

    /**
     * 创建退款申请单，商家在需要进行退款处理的时候需要向平台发起退款申请，又平台进行退款处理
     *
     * @param array $data 申请退款数据
     * @param int   $tid  订单编号
     * @param int   $oid  子订单编号
     */
    public function create($data, $tid, $oid)
    {
        $params = ['tid' => $tid, 'oid' => $oid, 'fields' =>'tid,post_fee,orders.payment'];
        $tradeData = app::get('sysaftersales')->rpcCall('trade.get', $params);

        if( $data['total_price']  > $tradeData['orders'][0]['payment'] )
        {
            throw new \LogicException(app::get('sysaftersales')->_('商品退款金额不能大于付款金额'));
        }

        $insertData['aftersales_bn'] = $data['aftersales_bn'];
        $insertData['refunds_reason'] = $data['refunds_reason'];
        $insertData['total_price'] =  $data['total_price'];
        $insertData['tid'] =  $data['tid'];
        $insertData['oid'] =  $data['oid'];
        $insertData['shop_id'] =  $data['shop_id'];
        $insertData['user_id'] =  $data['user_id'];
        $insertData['created_time'] = time();
        $insertData['modified_time'] = time();

        return $this->objMdlRefunds->insert($insertData);
    }

    public function updateStatus($aftersalesBn,$status)
    {
        return $this->objMdlRefunds->update(array('status'=>$status), array('aftersales_bn'=>$aftersalesBn));
    }
}

