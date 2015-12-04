<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysaftersales_apply {

    public function __construct()
    {
        $this->objMdlAftersales = app::get('sysaftersales')->model('aftersales');
    }

    /**
     * 消费者提交售后申请
     *
     * @param array $data 创建售后申请提交的参数
     */
    public function create($data)
    {
        $tradeFiltr = array(
            'tid' => $data['tid'],
            'oid' => $data['oid'],
            'fields' => 'tid,user_id,shop_id,status,orders.oid,orders.user_id,orders.sku_id,orders.num,orders.sendnum,orders.title',
        );
        $tradeInfo = app::get('sysaftersales')->rpcCall('trade.get', $tradeFiltr,'buyer');

        $this->__checkApply($tradeInfo, $data);

        $saveData['aftersales_bn'] = $this->__genAftersalesBn($tradeInfo['shop_id'],$data['user_id']);
        $saveData['user_id'] = $data['user_id'];
        $saveData['shop_id'] = $tradeInfo['shop_id'];
        $saveData['aftersales_type'] =  $data['aftersales_type'];
        $saveData['reason'] = $data['reason'];
        $saveData['tid'] = $data['tid'];
        $saveData['oid'] = $data['oid'];
        $saveData['evidence_pic'] = implode(',',$data['evidence_pic']);
        $saveData['description'] = $data['description'];
        $saveData['title'] = $tradeInfo['orders']['0']['title'];
        $saveData['num'] = $tradeInfo['orders']['0']['num'];
        $saveData['created_time'] = time();
        $saveData['modified_time'] = time();

        $db = app::get('sysaftersales')->database();
        $db->beginTransaction();

        try
        {
            $result = $this->objMdlAftersales->insert($saveData);
            if(!$result)
            {
                throw new \LogicException(app::get('sysaftersales')->_('售后单创建失败'));
            }

            $params= array(
                'oid' => $data['oid'],
                'tid' => $data['tid'],
                'user_id' => $saveData['user_id'],
                'aftersales_status' => 'WAIT_SELLER_AGREE',
            );

            try
            {
                app::get('sysaftersales')->rpcCall('order.aftersales.status.update', $params);
            }
            catch(\LogicException $e)
            {
                throw new \LogicException(app::get('sysaftersales')->_($e->getMessage()));
            }
            $db->commit();
        }
        catch(\Exceptionp $e)
        {
            $db->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * 生成售后编号
     *
     * @param int $shopId
     */
    private function __genAftersalesBn($shopId,$userId)
    {
        $baseRandNum = rand(0,49);
        $modShopId = str_pad($shopId%100,2,'0',STR_PAD_LEFT);
        $modUserId = str_pad($userId%100,2,'0',STR_PAD_LEFT);
        return date('ymdHi').str_pad($baseRandNum,2,'0',STR_PAD_LEFT).$modShopId.$modUserId;
    }

    /**
     * 检查售后申请的订单是否合法
     *
     * @param array $tradeInfo 申请售后的订单数据
     * @param array $data 申请的参数
     */
    private function __checkApply($tradeInfo, $data)
    {

        $aftersalesInfo = $this->objMdlAftersales->getRow('aftersales_bn', array('oid'=>$data['oid']));
        if( $aftersalesInfo )
        {
            throw new \LogicException(app::get('sysaftersales')->_('已申请过售后，不需要再进行申请'));
        }

        if( empty($data['reason']) )
        {
            throw new \LogicException(app::get('sysaftersales')->_('售后理由必选'));
        }

        if( !$tradeInfo )
        {
            throw new \LogicException(app::get('sysaftersales')->_('申请的订单不存在'));
        }

        if( $tradeInfo['user_id'] != $data['user_id'])
        {
            throw new \LogicException(app::get('sysaftersales')->_('申请的订单编号无权访问'));
        }

        //根据订单状态判断是否申请售后的类型是否可以可以进行申请
        switch( $data['aftersales_type'] )
        {
            case 'REFUND_GOODS'://退货退款
            case 'EXCHANGING_GOODS'://换货
                if( $tradeInfo['status'] != 'WAIT_BUYER_CONFIRM_GOODS' && $tradeInfo['status'] != "TRADE_FINISHED" )
                {
                    throw new \LogicException(app::get('sysaftersales')->_('该商品不能申请退换货'));
                }
                break;
            default://默认为只退款
                if( $tradeInfo['status'] == 'TRADE_FINISHED' || $tradeInfo['status'] == 'WAIT_BUYER_PAY' )
                {
                    throw new \LogicException(app::get('sysaftersales')->_('该商品不能申请退款'));
                }
                break;
        }

        return true;
    }

}
